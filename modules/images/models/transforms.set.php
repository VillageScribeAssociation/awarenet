<?

//--------------------------------------------------------------------------------------------------
//*	this object manages the set of allowed image dimensions as stored in registry
//--------------------------------------------------------------------------------------------------
//+	Image dimensions are stored in registry with a set of keys such as:
//+	
//+		images.size.thumb	:= 100x100
//+
//+	Where thumb is the URL friendly label and the value records the dimensions.  Dimensions with 
//+	only a single argument are fixed width, variable height.

class Images_Transforms {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;				//_	transforms, array of label => filename [dict]
	var $loaded = false;		//_	set to true when presets are loaded [bool]

	var $presets;				//_	array of image sizes [dict:dict]
	var $imageUID = '';			//_	UID of an Images_Image object [string]
	var $sourceFile = '';		//_	location of original uploaded file [string]
	var $image = -1;			//_	handle to GD image [int]
	var $aspect = -1;			//_	aspect ratio of image [float]
	var $width = 0;				//_	width of source image, pixels [int]
	var $height = 0;			//_	height of source image, pixels [int]

	var $lasterr = '';			//_	for debug info [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: imageUID - UID of an Images_Image object [string]
	//opt: sourceFile - location of original uploaded image [string]

	function Images_Transforms($imageUID = '', $sourceFile = '') {
		$this->members = array();
		$this->presets = array();

		$this->loadPresets();
		$this->imageUID = $imageUID;
		$this->sourceFile = $sourceFile;

		if (('' != $this->imageUID) && ('' != $this->sourceFile)) {
			//$this->load();
		}
	}

	//==============================================================================================	
	//	PRESET SIZES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load from registry
	//----------------------------------------------------------------------------------------------

	function loadPresets() {
		global $kapenta;
		$dims = $kapenta->registry->search('images', 'images.size.');	//%	set of registry keys [dict]
		$this->presets = array();								//	clear presets

		foreach($dims as $key => $value) {
			$meta = array(
				'label' => str_replace('images.size.', '', $key),
				'type' => 'unknown',
				'size' => $value,
				'watermark' => 'no',
				'width' => 0,
				'height' => 0,
			);

			if (false !== strpos($value, 'w')) { $meta['watermark'] = 'yes'; }
			$value = str_replace('w', '', $value);
			
			if (false != strpos($value, 'x')) {
				$parts = explode('x', $value);
				$meta['width'] = (int)$parts[0];
				$meta['height'] = (int)$parts[1];

				$meta['type'] = 'fixed_size';
				if ('*' == $meta['height']) { $meta['type'] = 'fixed_width'; }
				if ('*' == $meta['width']) { $meta['type'] = 'fixed_height'; }

				if (('*' == $meta['width']) && ('*' == $meta['height'])) {
					$meta['type'] = 'full_size';
				}

				$this->presets[$meta['label']] = $meta;
			}

		}

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a fixed image dimension, eg 100x100
	//----------------------------------------------------------------------------------------------
	//arg: label - name of image size, eg, 'thumbnail' or 'slide' [string]
	//arg: width - width of image, pixels [int]
	//arg: height - height of image, pixels [int]
	//returns: true on success, false on failure [bool]	

	function addPreset($label, $width, $height, $watermark) {
		if (false == $this->loaded) { $this->loadPresets(); }

		$label = strtolower(trim($label));
		if ('' == $label) { return false; }
		$key = 'images.size.' . $label;
		$value = (int)trim($width) . 'x' . (int)trim($height);
		$check = $kapenta->registry->set($key, $value);

		$this->loadPresets();											//	reload from registry
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a transform is registered
	//----------------------------------------------------------------------------------------------

	function presetExists($label) {
		if (false == $this->loaded) { $this->loadPresets(); }
		if (true == array_key_exists($label, $this->presets)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove an image dimension
	//----------------------------------------------------------------------------------------------
	//arg: label - name of image size [string]

	function removePreset($label) {
		global $kapenta;
		if (false == $this->loaded) { $this->loadPresets(); }

		$check = false;													//%	return value [bool]
		$key = 'images.size.' . trim(strtolower($label));
		if ('' == $kapenta->registry->get($key)) { return $check; }
		$check = $kapenta->registry->delete($key);

		$this->loadPresets();											// reload from registry
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the width of an image preset
	//----------------------------------------------------------------------------------------------
	//arg: label - name of image size [string]
	//returns: width in pixels, or -1 on failure [bool]

	function getWidth($label) {
		if (false == $this->loaded) { $this->loadPresets(); }
		if (false == array_key_exists($label, $this->presets)) { return -1; }
		return $this->presets[$label]['width'];
	}

	//==============================================================================================
	//	actual transforms
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	discover if a named transform exists
	//----------------------------------------------------------------------------------------------

	function has($label) {
		global $kapenta;
		if (false == $this->presetExists($label)) { return false; }

		$fileName = $this->fileName($label);
		if (true == $kapenta->fs->exists($fileName)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load list of transforms available on this instance
	//----------------------------------------------------------------------------------------------
	//;	note that presets should be loaded before this is called

	function load() {
		//	performance profiling showed all these fileExists to be wasteful - now using
		//	$this->loaded to refer to presets, strix, 2012-07-12
		/*
		global $kapenta;
		$this->members = array();
		foreach($this->presets as $preset) {
			$fileName = $this->fileName($preset['label']);
			if (true == $kapenta->fs->exists($fileName)) {
				$this->members[$preset['label']] = $fileName;
			}
		}
		$this->loaded = true;
		*/
	}

	//----------------------------------------------------------------------------------------------
	//.	make a transform from the current image
	//----------------------------------------------------------------------------------------------
	//arg: label - size of transform to make [string]
	//returns: true on success, false on failure [bool]

	function make($label) {
		global $session;
		if (false == $this->presetExists($label)) { return false; }
		//$kapenta->session->msgAdmin("Making image: $label<br/>");

		if ('full' == $label) { return true; }	//	magic, original image

		$check = false;							//%	return value [bool]
		$preset = $this->presets[$label];		//%	shortcut [array]

		switch($preset['type']) {

			case 'fixed_width':
				$img = $this->scaleToWidth($preset['width'], $label);
				if (false !== $img) { $check = true; }
				break;		//......................................................................


			case 'fixed_size':
				$img = $this->scaleToBox($preset['width'], $preset['height'], $label);
				if (false !== $img) { $check = true; }
				break;		//......................................................................
		}
		
		return $check;
	}

	//==============================================================================================
	//	image handling
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load the image from disk
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function loadImage() {
		global $kapenta;
		$check = false;												//%	return value [bool]
		$raw = $kapenta->fs->get($this->sourceFile);		//%	raw file contents [string]
		if (false === $raw) { return $check; }

		$this->image = imagecreatefromstring($raw);					//%	GD handle [int]

		if (false === $this->image) {
			$this->image = -1;
			$this->aspect = -1;
			$check = false;
		} else {
			$this->width = imagesx($this->image);
			$this->height = imagesy($this->image);
			$this->aspect = ($this->width / $this->height);
			$check = true;
		}

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	scale the image to a given width
	//----------------------------------------------------------------------------------------------
	//arg: width - width in pixels [int]
	//arg: label - name of preset size [string]
	//returns: handle to new image [int]

	function scaleToWidth($width, $label) {
		global $kapenta;
		global $session;

		if (-1 == $this->image) { $this->loadImage(); }
		if (-1 == $this->image) { return false; }

		$fileName = $this->fileName($label);							//%	output file [string]
		$check = false;													//%	return value [bool]
		$height = ceil($width / $this->aspect);							//%	dest height [int]
		$newImg = imagecreatetruecolor($width, $height);				//%	GD handle [int]


		$check = imagecopyresampled(
			$newImg, $this->image, 0, 0, 0, 0, 
			$width, $height, $this->width, $this->height
		);

		if (true == $check) { 
			$kapenta->fs->makePath($fileName);
			$check = imagejpeg($newImg, $fileName, 85);
			if (true == $check) { $this->members[$label] = $fileName; }
			else { $kapenta->session->msg('Could not save rescaled image.', 'bad'); }
		}

		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	scale and crop the image to fit a given box
	//----------------------------------------------------------------------------------------------
	//arg: width - box width [int]
	//arg: height - box height [int]
	//arg: label - name of preset size [string]
	//TODO: simplify and reduce this once tested

	function scaleToBox($width, $height, $label) {
		global $session;
		global $kapenta;

		if (-1 == $this->image) { $this->loadImage(); }
		if (-1 == $this->image) { return false; }

		$fileName = $this->fileName($label);							//%	output file [string]
		$toAspect = ($width / $height);									//%	aspect ratio [float]
		$newImg = imagecreatetruecolor($width, $height);				//%	GD handle [int]
		$log = '';														//%	debug log [string]
		$check = false;													//%	return value [bool]

		$log .= ''
		 . "fileName: " . $fileName . "<br/>\n"
		 . "source aspect ratio: " . $this->aspect . "<br/>\n"
		 . "dest aspect ratio: $toAspect<br/>\n"
		 . "dest width: $width<br/>\n"
		 . "dest height: $height<br/>\n";
		
		if ($toAspect > $this->aspect) {
			//--------------------------------------------------------------------------------------
			// resize to common width, trim top and bottom edges
			//--------------------------------------------------------------------------------------
			// think: if they had the same width, what would be the difference in height?
			$scaleHeight = ($width / $this->aspect);
			$destY = ($height - $scaleHeight) / 2;
			$log .= "scaleHeight: $scaleHeight <br/>\n";
			$log .= "destY: $destY <br/>\n";			

			$check = imagecopyresampled(
				$newImg, $this->image, 0, $destY, 0, 0, 
				$width, $scaleHeight, $this->width, $this->height
			);
			
		} else {		
			//--------------------------------------------------------------------------------------
			// resize to common height, trim left and right edges
			//--------------------------------------------------------------------------------------
			// think: if they had the same height, what would be the difference in width?
			$scaleWidth = ($height * $this->aspect);
			$destX = ($width - $scaleWidth) / 2;
			$log .= "scale width: $scaleWidth <br/>\n";
			$log .= "dest x: $destX <br/>\n";
	
			$check = imagecopyresampled(
				$newImg, $this->image, $destX, 0, 0, 0, 
				$scaleWidth, $height, $this->width, $this->height
			);
			
		}
		
		if (true == $check) {
			$kapenta->fs->makePath($fileName);
			$kapenta->fs->put($fileName, '', true); 
			$check = imagejpeg($newImg, $kapenta->installPath . $fileName, 85);
			if (true == $check) { $this->members[$label] = $fileName; }
			else { $kapenta->session->msg('Could not save rescaled image.', 'bad'); }
		}

		//$kapenta->session->msgAdmin($log);
		//echo $log;
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	enforce filsize / resolution limits on this image
	//----------------------------------------------------------------------------------------------
	//;	assumes that 'widthmax' preset exists (by default 1024px)
	//;
	//returns: true if reduced, false if not [string]

	function reduce() {
		global $kapenta;
		global $kapenta;
		global $session;

		$this->lasterr = '';

		$fileSize = $kapenta->fs->size($this->sourceFile);
		$maxSize = (int)$kapenta->registry->get('images.maxsize');

		if ($fileSize < $maxSize) {return true; }					//	no need to reduce

		if (false == $this->loaded) {
			$this->lasterr = "No object loaded.";
			return false;
		}

		if (false == $this->loadImage()) {
			$this->lasterr = "Could not loadImage(), invalid?";
			return false;
		}

		if ($this->width <= 1024) {									//	(TODO) use registry
			$this->lasterr = "Too small to reduce.";
			return true;
		}

		if ('' == $kapenta->registry->get('images.size.widthmax')) {
			$kapenta->registry->set('images.size.widthmax', '1024x*');
			$kapenta->session->msg("Set default 'widthmax' preset: 1024x*.");
		}

		//	scale down
		$check = $this->make('widthmax');

		if (false == $check) {
			$this->lasterr = "Failed to apply transform 'widthmax'.";
			return false;
		}

		//	swap the new transform for the original
		$newFile = $this->fileName('widthmax');

		if (false == $kapenta->fs->exists($newFile)) {
			$this->lasterr = "Transform file could not be created, check directory permissions.";
			return false;
		}

		if (false == $kapenta->fs->exists($this->sourceFile)) {
			$this->lasterr = 'Source file does not exist to make tranform from.';
			return false;
		}

		$kapenta->fs->delete($this->sourceFile, true);
		$kapenta->fs->copy($newFile, $this->sourceFile);

		$msg = ''
		 . "Reduced image '" . $this->imageUID . "' to maximum width.  "
		 . "<a href='" . $kapenta->serverPath . $this->sourceFile . "'>[see]</a>";

		$kapenta->session->msg($msg, 'ok');
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a transform file name
	//----------------------------------------------------------------------------------------------

	function fileName($label) {
		$UID = $this->imageUID;
		$UID = str_replace('/', '', $UID);		//	unlikely but just in case
		$UID = str_replace('\\', '', $UID);		//	...

		$fileName = 'data/images/'
		 . substr($UID, 0, 1) . '/'
		 . substr($UID, 1, 1) . '/'
		 . substr($UID, 2, 1) . '/'
		 . $UID . '_' . $label . '.jpg';

		return $fileName;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete all tranform files
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function deleteAll() {
		global $kapenta;
		$check = true;
		foreach($this->members as $label => $fileName) {
			if (false == $kapenta->fs->delete($fileName)) { $check = false; }
		}
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	display as HTML table (for settings page, debugging, etc)
	//----------------------------------------------------------------------------------------------
	//returns: html report of image presets [string]

	function toHtml() {
		global $theme;

		$table = array();
		$table[] = array('Label', 'Type', 'Scale', '[x]');
		$block = $theme->loadBlock('modules/images/views/deletepreset.block.php');

		foreach($this->presets as $label => $meta) {
			$delBtn = str_replace('%%label%%', $label, $block);
			$table[] = array($label, $meta['type'], $meta['size'], $delBtn);
		}

		$html = $theme->arrayToHtmlTable($table, true, true);
		return $html;
	}

}

?>
