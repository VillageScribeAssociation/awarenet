<?

	require_once($kapenta->installPath . 'modules/images/inc/imageset.class.php');

//--------------------------------------------------------------------------------------------------
//*	object for managing images
//--------------------------------------------------------------------------------------------------
//+	The following types are supported: jpeg, gif, png
//+
//+	Transforms are derivative images that do not need their own record, such as thumbnails.  They 
//+	are automatically created as needed and destroyed if unused for a period of time, to free disk 
//+	space.
//+
//+	examples: /images/width300/someimage.jpg /images/thumb/someimage.jpg
//+
//+	Transform scripts can be modifed to perform actions such as automatically watermarking images
//+	uploaded to a website.

class Images_Image {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*-* [string]
	var $title;				//_ title [string]
	var $licence;			//_ varchar(30) [string]
	var $attribName;		//_ varchar(255) [string]
	var $attribUrl;			//_ varchar(255) [string]
	var $fileName;			//_ varchar(255) [string]
	var $format;			//_ varchar(30) [string]
	var $transforms;		//_ plaintext [string]
	var $caption;			//_ plaintext [string]
	var $category;			//_ varchar(100) [string]
	var $weight;			//_ int [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	var $img;				// image handle

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Image object [string]

	function Images_Image($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Image ' . $this->UID;		// set default title
			$this->transforms = array();					// no transforms yet
			$this->weight = 10000;							// end of list (corrected on save())
			$this->loaded = false;
		}
	}
	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Image object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Image object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->title = $ary['title'];
		$this->licence = $ary['licence'];
		$this->attribName = $ary['attribName'];
		$this->attribUrl = $ary['attribUrl'];
		$this->fileName = $ary['fileName'];
		$this->format = $ary['format'];
		$this->transforms = $this->expandTransforms($ary['transforms']);
		$this->caption = $ary['caption'];
		$this->category = $ary['category'];
		$this->weight = $ary['weight'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('images', 'Images_Image', $this->UID, $this->title);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }

		// update image weights
		$set = new Images_Imageset($this->refModule, $this->refModel, $this->UID);
		$set->checkWeights();

		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'images';
		$dbSchema['model'] = 'Images_Image';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'licence' => 'VARCHAR(30)',
			'attribName' => 'VARCHAR(255)',
			'attribUrl' => 'VARCHAR(255)',
			'fileName' => 'VARCHAR(255)',
			'format' => 'VARCHAR(30)',
			'transforms' => 'TEXT',
			'caption' => 'TEXT',
			'category' => 'VARCHAR(100)',
			'weight' => 'BIGINT(20)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'weight' );

		return $dbSchema;
		
	} // end getDbSchema

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'title' => $this->title,
			'licence' => $this->licence,
			'attribName' => $this->attribName,
			'attribUrl' => $this->attribUrl,
			'fileName' => $this->fileName,
			'format' => $this->format,
			'transforms' => $this->collapseTransforms($this->transforms),
			'caption' => $this->caption,
			'category' => $this->category,
			'weight' => $this->weight,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '', $oldFormat = false) {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xType = 'kobject';
		if (true == $oldFormat) { $xType = 'image'; }

		$xml = $indent . "<$xType type='Images_Image'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <licence>" . $this->licence . "</licence>\n"
			. $indent . "    <attribName>" . $this->attribName . "</attribName>\n"
			. $indent . "    <attribUrl>" . $this->attribUrl . "</attribUrl>\n"
			. $indent . "    <fileName>" . $this->fileName . "</fileName>\n"
			. $indent . "    <format>" . $this->format . "</format>\n"
			. $indent . "    <transforms>" . $this->transforms . "</transforms>\n"
			. $indent . "    <caption>" . $this->caption . "</caption>\n"
			. $indent . "    <category>" . $this->category . "</category>\n"
			. $indent . "    <weight>" . $this->weight . "</weight>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <alias>" . $this->alias . "</alias>\n"
			. $indent . "</$xType>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}


	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('images', 'Images_Image', 'show', $this->UID)) {
			$ext['viewUrl'] = "";
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('images', 'Images_Image', 'edit', $this->UID)) {
			$ext['editUrl'] = '%~%serverPath%~%Images/editimage/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('images', 'Images_Image', 'delete', $this->UID)) {
			$ext['delUrl'] = '%~%serverPath%~%Images/delimage/' . $ext['alias'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand transforms
	//----------------------------------------------------------------------------------------------
	//arg: serialized - serialized transforms [string]
	//returns: array of transforms, size => fileName [string]

	function expandTransforms($serialized) {
		$transforms = array();
		$lines = explode("\n", $serialized);
		foreach($lines as $line) {
		  $pipe = strpos($line, '|');
		  if ($pipe != false) {
			$transName = substr($line, 0, $pipe);
			$transFile = substr($line, $pipe + 1);
			$this->transforms[$transName] = $transFile;
		  }
		}
		return $transforms;
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse transforms
	//----------------------------------------------------------------------------------------------
	//arg: transforms - transforms array [array]
	//returns: transforms serialized to string [string]

	function collapseTransforms($transforms) {
		$serialized = '';								//%	return value [string]
		foreach($transforms as $transName => $transFile) 
			{ $serialized .= $transName . '|' . $transFile . "\n"; }

		return $serialized;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if a given transform exists
	//----------------------------------------------------------------------------------------------
	//arg: transName - name of a transform, eg thumb [string]
	//returns: location of transformed file if it exists, false if not [string][bool]

	function hasTrasform($transName) {
		global $installPath;
		if (array_key_exists($transName, $this->transforms) == false) { return false; }
		if (file_exists($installPath . $this->transforms[$transName])) { 
			return $this->transforms[$transName];
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load actual image (rather than the record)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function loadImage() {
		global $kapenta;
		if (false == $kapenta->fileExists($this->fileName)) { return false; }
		$fileName = $kapenta->installPath . $this->fileName;
		if ('jpg' == $this->format) { $this->img = @imagecreatefromjpeg($fileName); }
		if ('png' == $this->format) { $this->img = @imagecreatefrompng($fileName); }
		if ('gif' == $this->format) { $this->img = @imagecreatefromgif($fileName); }
		if (false == $this->img) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	scale the image to a given width
	//----------------------------------------------------------------------------------------------
	//arg: width - width in pixels [int]
	//returns: handle to new image [int]

	function scaleToWidth($width) {
		$aspect = (imagesx($this->img) / imagesy($this->img));
		$height = ($width / $aspect);
		$newImg = imagecreatetruecolor($width, $height);
		imagecopyresampled($newImg, $this->img, 0, 0, 0, 0, 
					$width, $height, imagesx($this->img), imagesy($this->img));

		return $newImg;
	}

	//----------------------------------------------------------------------------------------------
	//.	scale and crop the image to fit a given box
	//----------------------------------------------------------------------------------------------
	//arg: toWidth - box width [int]
	//arg: toHeight - box height [int]
	//returns: handle of new image [int]

	function scaleToBox($toWidth, $toHeight) {
		$srcAspect = (imagesx($this->img) / imagesy($this->img));
		$destAspect = $toWidth / $toHeight;
		$newImg = imagecreatetruecolor($toWidth, $toHeight);

		$chatty = '';		
		$chatty .= "source aspect ratio: $srcAspect <br/>\n";
		$chatty .= "dest aspect ratio: $destAspect <br/>\n";
		$chatty .= "dest width: $toWidth <br/>";
		$chatty .= "dest height: $toHeight <br/>";
		
		if ($destAspect > $srcAspect) {
			//--------------------------------------------------------------------------------------
			// resize to common width, trim top and bottom edges
			// think: if they had the same width, what would be the difference in height?
			//--------------------------------------------------------------------------------------
		
			$scaleHeight = ($toWidth / $srcAspect);
			$destY = ($toHeight - $scaleHeight) / 2;
			$chatty .= "scaleHeight: $scaleHeight <br/>\n";
			$chatty .= "destY: $destY <br/>\n";			
			$chatty .= "here ---!--- ";

			imagecopyresampled(
				$newImg, $this->img, 
				0, $destY, 
				0, 0, 
				$toWidth, $scaleHeight, 
				imagesx($this->img), imagesy($this->img)
			);			
			
		} else {		
			//--------------------------------------------------------------------------------------
			// resize to common height, trim left and right edges
			// think: if they had the same height, what would be the difference in width?
			//--------------------------------------------------------------------------------------
			
			$scaleWidth = ($toHeight * $srcAspect);
			$destX = ($toWidth - $scaleWidth) / 2;
		
			$chatty .= "scale width: $scaleWidth <br/>\n";
			$chatty .= "dest x: $destX <br/>\n";
	
			imagecopyresampled(
				$newImg, $this->img, 
				$destX, 0, 
				0, 0, 
				$scaleWidth, $toHeight, 
				imagesx($this->img), imagesy($this->img)
			);
			
		}
		
		//echo $chatty;
		return $newImg;
	}

	//----------------------------------------------------------------------------------------------
	//.	find a single image on a given object and module, and load it
	//----------------------------------------------------------------------------------------------
	//arg: refModule - module which controls this image's owner [string]
	//arg: refUID - UID of object which owns this image [string]
	//arg: category - unused at present [string]
	//returns: UID of image, or false if one was not found [string][bool]

	function findSingle($refModule, $refUID, $category) {
		global $db;

		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$conditions[] = "category='" . $db->addMarkup($category) . "'";
		$range = $db->loadRange('Images_Image', '*', $conditions, 'weight', '1', '');

		foreach($range as $row) {
			$this->load($row['UID']); 
			return $row['UID']; 
		}

		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	save an image to disk and record the filename in $this->fileName
	//----------------------------------------------------------------------------------------------
	//arg: img - an image handle [int]

	function storeFile($img) {
		global $kapenta;
		//TODO: make JPEG quality a setting
		//------------------------------------------------------------------------------------------
		//	save the file
		//------------------------------------------------------------------------------------------
		$baseDir = 'data/images/'
			 . substr($this->UID, 0, 1) . '/'
			 . substr($this->UID, 1, 1) . '/'
			 . substr($this->UID, 2, 1) . '/';

		$this->fileName = $baseDir . $this->UID . '.jpg';
		$kapenta->fileMakeSubdirs($this->fileName, true);				// ensure directory exists
		imagejpeg($img, $kapenta->installPath . $this->fileName, 95);	// save it
		$this->format = 'jpg';
	}

	//---------------------------------------------------------------------------------------------
	//.	nominally delete the current record, dissociate from owner
	//---------------------------------------------------------------------------------------------
	//, due to some messed up events of March 2010, images are not deleted first time round

	function delete() {
		$ext = $this->extArray();

		$this->refUID  = str_replace('del-', '', $this->refUID);
		$this->refModule  = str_replace('del-', '', $this->refModule);

		$this->refUID = 'del-' . $this->refUID;
		$this->refModule = 'del-' . $this->refModule;
		$this->save();

		//------------------------------------------------------------------------------------------
		//	send specific event to module responsible for object which owned the deleted image
		//------------------------------------------------------------------------------------------
		//$args = array('module' => 'images', 'UID' => $this->UID, 'title' => $ext['title']);
		//eventSendSingle($ext['refModule'], 'images_deleted', $args);
		//TODO: this
	}

	//---------------------------------------------------------------------------------------------
	//.	delete current image and all transforms
	//---------------------------------------------------------------------------------------------

	function finalDelete() {
		global $kapenta, $db;;
		$ext = $this->extArray();

		//-----------------------------------------------------------------------------------------
		//	delete the file and any transforms
		//-----------------------------------------------------------------------------------------
		$this->expandTransforms();
		foreach($this->transforms as $transName => $fileName) {
			$fileName = $kapenta->installPath . $fileName;
			if (file_exists($fileName) == true) { @unlink($fileName); }
		}
		
		if (file_exists($kapenta->installPath . $this->fileName) == true) 
			{ @unlink($installPath . $this->fileName); }		// TODO: kapenta should hand this

		//-----------------------------------------------------------------------------------------
		//	delete the record
		//-----------------------------------------------------------------------------------------
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;

		//-----------------------------------------------------------------------------------------
		//	send specific event to module responsible for object which owned the deleted image
		//-----------------------------------------------------------------------------------------
		//$args = array('module' => 'images', 'UID' => $this->UID, 'title' => $ext['title']);
		//eventSendSingle($ext['refModule'], 'image_deleted', $args);
		//TODO: this
	}

}

?>
