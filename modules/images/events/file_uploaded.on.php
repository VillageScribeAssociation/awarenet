<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an image has been uploaded by the live module
//--------------------------------------------------------------------------------------------------

function images__cb_file_uploaded($args) {
	global $kapenta;
	global $session;
	global $utils;

	$msg = ''
	 . "File Uploaded<br>"
	 . "refModule: " . $args['refModule'] . "<br/>"
	 . "refModel: " . $args['refModel'] . "<br/>"
	 . "refUID: " . $args['refUID'] . "<br/>"
	 . "path: " . $args['path'] . "<br/>"
	 . "type: " . $args['type'] . "<br/>"
	 . "extension: " . $args['extension'] . "<br/>";
	//$session->msg($msg);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('path', $args)) { return false; }

	if ('image' != $args['type']) {
		$session->msg('Uploaded file was not an image.');
		return false;
	}

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check that this is actually an image
	//----------------------------------------------------------------------------------------------

	$raw = $kapenta->fileGetContents($args['path']);	//%	contents of uploaded file [string]
	$gdh = imagecreatefromstring($raw);					//%	GD image handle [int]

	if (false == $gdh) {
		$session->msg('Uploaded file was not a valid image.', 'bad');
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	create a new images_image object attached to this gallery
	//----------------------------------------------------------------------------------------------

	$model = new Images_Image();
	$model->UID = $kapenta->createUID();
	$model->refModule = $args['refModule'];
	$model->refModel = $args['refModel'];
	$model->refUID = $args['refUID'];
	$model->title = $utils->cleanTitle($args['name']);
	$model->format = $args['extension'];
	$model->shared = 'yes';

	$model->fileName = ''
	 . 'data/images/'
	 . substr($model->UID, 0, 1) . '/'
	 . substr($model->UID, 1, 1) . '/'
	 . substr($model->UID, 2, 1) . '/'
	 . $model->UID . '.jpg';

	$report = $model->save();

	if ('' == $report) {
		$check = $kapenta->fileCopy($args['path'], $model->fileName);
		$session->msg('Attached image.');

		//------------------------------------------------------------------------------------------
		//	image was uploaded correctly raise file_added event for this image (p2p uses it)
		//------------------------------------------------------------------------------------------
		$detail = array(
			'refModule' => 'images', 
			'refModel' => 'images_image', 
			'refUID' => $model->UID, 
			'fileName' => $model->fileName, 
			'hash' => $kapenta->fileSha1($model->fileName),
			'size' => $kapenta->fileSize($model->fileName)
		);

		$kapenta->raiseEvent('*', 'file_added', $detail);

		//------------------------------------------------------------------------------------------
		//	send 'images_added' event to module whose record owns this image
		//------------------------------------------------------------------------------------------
		$detail = array(	
			'refModule' => $model->refModule, 
			'refModel' => $model->refModel, 
			'refUID' => $model->refUID, 
			'imageUID' => $model->UID, 
			'imageTitle' => $model->title    
		);

		$kapenta->raiseEvent('*', 'images_added', $detail);
		$session->msgAdmin('Attached image file.');

	} else {
		$session->msg('Could not create image object.');
	}

}

?>
