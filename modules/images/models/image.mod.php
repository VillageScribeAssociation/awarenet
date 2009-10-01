<?

//--------------------------------------------------------------------------------------------------
//	object for managing images
//--------------------------------------------------------------------------------------------------
//	
//	The following types are supported: jpeg, gif, png
//
//	Transforms are derivative images that do not need their own record, such as thumbnails.  They 
//	are automatically created as needed and destroyed if unused for a period of time, to free disk 
//	space.
//
//	examples: /images/width300/someimage.jpg /images/thumb/someimage.jpg
//
//	Transform scripts can be modifed to perform actions such as automatically watermarking images
//	uploaded to a website.

class Image {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure
	var $transforms;	// array of transforms (derivative images)
	var $img;			// image handle

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Image($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['createdOn'] = mysql_datetime();
		$this->data['createdBy'] = $_SESSION['sUserUID'];
		$this->data['createdOn'] = mysql_datetime();
		$this->data['fileName'] = '';
		$this->transforms = array();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('images', $uid, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		$this->expandTransforms();
		return true;
	}
	
	function loadArray($ary) {
		$this->data = $ary;
		$this->expandTransforms();
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('images', $d['UID'], $d['title'] . '.jpg','images');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		// TODO
		$ary = $this->data;	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

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
	//	expand transforms
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
	//	check if a given transform exists
	//----------------------------------------------------------------------------------------------

	function hasTrasform($transName) {
		global $installPath;
		if (array_key_exists($transName, $this->transforms) == false) { return false; }
		if (file_exists($installPath . $this->transforms[$transName])) { 
			return $this->transforms[$transName];
		}
	}

	//----------------------------------------------------------------------------------------------
	//	load actual image (rather than the record)
	//----------------------------------------------------------------------------------------------

	function loadImage() {
		global $installPath;
		$fileName = $installPath . $this->data['fileName'];
		if ($this->data['format'] == 'jpg') { $this->img = imagecreatefromjpeg($fileName); }
		if ($this->data['format'] == 'png') { $this->img = imagecreatefrompng($fileName); }
		if ($this->data['format'] == 'gif') { $this->img = imagecreatefromgif($fileName); }
	}

	//----------------------------------------------------------------------------------------------
	//	scale the image to a given width
	//----------------------------------------------------------------------------------------------

	function scaleToWidth($width) {
		$aspect = (imagesx($this->img) / imagesy($this->img));
		$height = ($width / $aspect);
		$newImg = imagecreatetruecolor($width, $height);
		imagecopyresampled($newImg, $this->img, 0, 0, 0, 0, 
					$width, $height, imagesx($this->img), imagesy($this->img));

		return $newImg;
	}

	//----------------------------------------------------------------------------------------------
	//	scale and crop the image to fit a given box
	//----------------------------------------------------------------------------------------------

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
	//	find a single image on a given record and module
	//----------------------------------------------------------------------------------------------

	function findSingle($refModule, $refUID, $category) {
		$sql = "select * from images where refModule='" . sqlMarkup($refModule) 
		     . "' and refUID='" . sqlMarkup($refUID) 
			 . "' and category = '" . sqlMarkup($category) . "'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { 
			$this->load($row['UID']); 
			return $row['UID']; 
		}

		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//	save an image to disk and record the filename in $this->fileName
	//----------------------------------------------------------------------------------------------

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

	//----------------------------------------------------------------------------------------------
	//	delete current image and all transforms
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $installPath;
		if ($this->data['fileName'] == '') { return false; }
		
		$this->expandTransforms();
		foreach($this->transforms as $transName => $fileName) {
			$fileName = $installPath . $fileName;
			@unlink($fileName);
		}
		
		@unlink($installPath . $this->data['fileName']);
		raDeleteAll('images', $this->data['UID']);
		dbDelete('images', $this->data['UID']);
	}
	
}

?>
