<?

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

class Image {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure
	var $transforms;	// array of transforms (derivative images)
	var $img;			// image handle

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of an announcement [string]

	function Image($raUID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['fileName'] = '';
		$this->transforms = array();
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of an announcement record [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		$ary = dbLoadRa('images', $raUID, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		$this->expandTransforms();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]
	
	function loadArray($ary) {
		$this->data = $ary;
		$this->expandTransforms();
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$ra = raSetAlias('images', $this->data['UID'], $this->data['title'] . '.jpg','images');
		$this->data['recordAlias'] = $ra;
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'images';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'refUID' => 'VARCHAR(30)',
			'refModule' => 'VARCHAR(30)',			
			'title' => 'VARCHAR(255)',
			'licence' => 'VARCHAR(100)',
			'attribName' => 'VARCHAR(255)',
			'attribURL' => 'VARCHAR(255)',
			'fileName' => 'VARCHAR(255)',
			'format' => 'VARCHAR(255)',
			'transforms' => 'TEXT',
			'caption' => 'TEXT',
			'category' => 'VARCHAR(100)',
			'weight' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'hitcount' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'refUID' => '10',
			'refModule' => '10',  
			'recordAlias' => '20', 
			'category' => '20' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias', 'hitcount', 'transforms');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		// TODO?
		$ary = $this->data;	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
		$report = "<h3>Installing Images Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('images') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created images table and indices...<br/>';
		} else {
			$this->report .= 'images table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand transforms
	//----------------------------------------------------------------------------------------------

	function expandTransforms() {
		$this->transforms = array();
		$lines = explode("\n", $this->data['transforms']);
		foreach($lines as $line) {
		  $pipe = strpos($line, '|');
		  if ($pipe != false) {
			$transName = substr($line, 0, $pipe);
			$transFile = substr($line, $pipe + 1);
			$this->transforms[$transName] = $transFile;
		  }
		}
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

	function loadImage() {
		global $installPath;
		$fileName = $installPath . $this->data['fileName'];
		if (file_exists($fileName) == false) { return false; }
		if ($this->data['format'] == 'jpg') { $this->img = imagecreatefromjpeg($fileName); }
		if ($this->data['format'] == 'png') { $this->img = imagecreatefrompng($fileName); }
		if ($this->data['format'] == 'gif') { $this->img = imagecreatefromgif($fileName); }
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
	//.	find a single image on a given record and module, and load it
	//----------------------------------------------------------------------------------------------
	//arg: refModule - module which controls this image's owner [string]
	//arg: refUID - UID of object which owns this image [string]
	//arg: category - unused at present [string]
	//returns: UID of image, or false if one was not found [string][bool]

	function findSingle($refModule, $refUID, $category) {
		$sql = "select * from images where refModule='" . sqlMarkup($refModule) 
		     . "' and refUID='" . sqlMarkup($refUID) 
			 . "' and category = '" . sqlMarkup($category) . "'";

		//TODO: dbLoadRange

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { 
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
		global $installPath;
		
		//------------------------------------------------------------------------------------------
		//	ensure directory exists
		//------------------------------------------------------------------------------------------
		$baseDir = $installPath . 'data/images/';
		$baseDir .= substr($this->data['UID'], 0, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->data['UID'], 1, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->data['UID'], 2, 1) . '/';
		@mkdir($baseDir);
		
		//------------------------------------------------------------------------------------------
		//	save the file
		//------------------------------------------------------------------------------------------
		$fileName = $baseDir . $this->data['UID'] . '.jpg';
		imagejpeg($img, $fileName, 95);
		
		$this->data['fileName'] = str_replace($installPath, '', $fileName);
		$this->data['format'] = 'jpg';
	}

	//---------------------------------------------------------------------------------------------
	//.	nominally delete the current record, dissociate from owner
	//---------------------------------------------------------------------------------------------
	//, due to some messed up events of March 2010, images are not deleted first time round

	function delete() {
		$ext = $this->extArray();

		$this->data['refUID']  = str_replace('del-', '', $this->data['refUID']);
		$this->data['refModule']  = str_replace('del-', '', $this->data['refModule']);

		$this->data['refUID'] = 'del-' . $this->data['refUID'];
		$this->data['refModule'] = 'del-' . $this->data['refModule'];
		$this->save();

		//-----------------------------------------------------------------------------------------
		//	send specific event to module responsible for object which owned the deleted image
		//-----------------------------------------------------------------------------------------
		$args = array('module' => 'images', 'UID' => $this->data['UID'], 'title' => $ext['title']);
		eventSendSingle($ext['refModule'], 'images_deleted', $args);

		//-----------------------------------------------------------------------------------------
		//	allow other modules to respond to this event
		//-----------------------------------------------------------------------------------------
		$args = array('module' => 'images', 'UID' => $this->data['UID']);
		eventSendAll('object_deleted', $args);

	}

	//---------------------------------------------------------------------------------------------
	//.	delete current image and all transforms
	//---------------------------------------------------------------------------------------------

	function finalDelete() {
		global $installPath;
		$ext = $this->extArray();

		//-----------------------------------------------------------------------------------------
		//	delete the file and any transforms
		//-----------------------------------------------------------------------------------------
		$this->expandTransforms();
		foreach($this->transforms as $transName => $fileName) {
			$fileName = $installPath . $fileName;
			if (file_exists($fileName) == true) { @unlink($fileName); }
		}
		
		if (file_exists($installPath . $this->data['fileName']) == true) 
			{ @unlink($installPath . $this->data['fileName']); }

		//-----------------------------------------------------------------------------------------
		//	delete the record
		//-----------------------------------------------------------------------------------------
		dbDelete('images', $this->data['UID']);

		//-----------------------------------------------------------------------------------------
		//	send specific event to module responsible for object which owned the deleted image
		//-----------------------------------------------------------------------------------------
		$args = array('module' => 'images', 'UID' => $this->data['UID'], 'title' => $ext['title']);
		eventSendSingle($ext['refModule'], 'image_deleted', $args);

		//-----------------------------------------------------------------------------------------
		//	allow other modules to respond to this event
		//-----------------------------------------------------------------------------------------
		$args = array('module' => 'images', 'UID' => $this->data['UID']);
		eventSendAll('object_deleted', $args);
	}

	//----------------------------------------------------------------------------------------------
	//.	bump up the hitcount by one
	//----------------------------------------------------------------------------------------------

	function incHitCount() {
		dbUpdateQuiet('images', $this->data['UID'], 'hitcount', ($this->data['hitcount'] + 1));
	}
	
}

?>
