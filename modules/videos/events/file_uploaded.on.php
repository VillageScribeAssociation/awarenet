<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an image has been uploaded by the live module
//--------------------------------------------------------------------------------------------------

function videos__cb_file_uploaded($args) {
	global $kapenta;
	global $session;
	global $utils;

	$msg = ''
	 . "File Uploaded<br>"
	 . "refModule: " . $args['refModule'] . "<br/>"
	 . "refModel: " . $args['refModel'] . "<br/>"
	 . "refUID: " . $args['refUID'] . "<br/>"
	 . "type: " . $args['type'] . "<br/>"
	 . "extension: " . $args['extension'] . "<br/>"
	 . "path: " . $args['path'] . "<br/>";
	$kapenta->session->msgAdmin($msg);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('path', $args)) { return false; }

	if ('video' != $args['type']) { return false; }
	if (
		('flv' != $args['extension']) &&
		('mp3' != $args['extension']) &&
		('mp4' != $args['extension'])
	) { return false; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	create a new videos_video object attached to this gallery
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Video();
	$model->UID = $kapenta->createUID();
	$model->refModule = $args['refModule'];
	$model->refModel = $args['refModel'];
	$model->refUID = $args['refUID'];
	$model->title = $utils->cleanTitle($args['name']);
	$model->format = $args['extension'];
	$model->shared = 'yes';
	$model->size = '0';
	$model->hash = '';

	$model->fileName = ''
	 . 'data/videos/'
	 . substr($model->UID, 0, 1) . '/'
	 . substr($model->UID, 1, 1) . '/'
	 . substr($model->UID, 2, 1) . '/'
	 . $model->UID . '.' . $args['extension'];

	$report = $model->save();

	if ('' == $report) {
		$check = $kapenta->fileCopy($args['path'], $model->fileName);
		$kapenta->session->msg('Attached video.');

		//------------------------------------------------------------------------------------------
		//	video was uploaded correctly raise file_added event
		//------------------------------------------------------------------------------------------
		$detail = array(
			'refModule' => $model->refModule, 
			'refModel' => $model->refModel, 
			'refUID' => $model->refUID, 
			'fileName' => $model->fileName, 
			'hash' => $kapenta->fileSha1($model->fileName),
			'size' => $kapenta->fs->size($model->fileName)
		);

		$kapenta->raiseEvent('*', 'file_added', $detail);

	} else {
		$kapenta->session->msg('Could not create video object.');
	}

}

?>
