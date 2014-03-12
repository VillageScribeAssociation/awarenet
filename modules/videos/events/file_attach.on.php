<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a file has been uploaded by the live module
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which owns this video [string]
//arg: refUID - UID of object which own this video [string]
//arg: path - location of file [string]
//arg: srcName - original location of file [string]
//arg: name - original name file [string]
//arg: ext - file extension [string]
//arg: module - module which handles files of this type [string]

function videos__cb_file_attach($args) {
	global $kapenta;
	global $session;
	global $utils;

	$msg = ''
	 . "File Uploaded<br>"
	 . "refModule: " . $args['refModule'] . "<br/>"
	 . "refModel: " . $args['refModel'] . "<br/>"
	 . "refUID: " . $args['refUID'] . "<br/>"
	 . "path: " . $args['path'] . "<br/>"
	 . "srcName: " . $args['srcName'] . "<br/>"
	 . "extension: " . $args['extension'] . "<br/>";
	$kapenta->session->msg($msg);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('path', $args)) { return false; }

	if ('videos' != $args['module']) { return false; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check that this is actually a video
	//----------------------------------------------------------------------------------------------
	//TODO: use mplayer or ffmpeg to do this where available

	//----------------------------------------------------------------------------------------------
	//	create a new videos_video object attached to this object
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Video();
	$model->UID = $kapenta->createUID();
	$model->refModule = $args['refModule'];
	$model->refModel = $args['refModel'];
	$model->refUID = $args['refUID'];
	$model->title = $utils->cleanTitle($args['name']);
	$model->format = $args['extension'];
	$model->shared = 'yes';

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
		//	video was uploaded correctly raise file_added event for this video (p2p uses it)
		//------------------------------------------------------------------------------------------
		$detail = array(
			'refModule' => 'videos', 
			'refModel' => 'videos_video', 
			'refUID' => $model->UID, 
			'fileName' => $model->fileName, 
			'hash' => $kapenta->fileSha1($model->fileName),
			'size' => $kapenta->fs->size($model->fileName)
		);

		$kapenta->raiseEvent('*', 'file_added', $detail);

		//------------------------------------------------------------------------------------------
		//	send 'videos_added' event to module whose record owns this video
		//------------------------------------------------------------------------------------------
		$detail = array(	
			'refModule' => $model->refModule, 
			'refModel' => $model->refModel, 
			'refUID' => $model->refUID, 
			'videoUID' => $model->UID, 
			'videoTitle' => $model->title    
		);

		$kapenta->raiseEvent('*', 'videos_added', $detail);
		$kapenta->session->msgAdmin('Attached video file.');

	} else {
		$kapenta->session->msg('Could not create video object.');
	}

	//----------------------------------------------------------------------------------------------
	//	try extract a thumbnail of this video
	//----------------------------------------------------------------------------------------------
	$detail = array(
		'refModule' => 'videos',
		'refModel' => 'videos_video',
		'refUID' => $model->UID,
		'fileName' => $model->fileName,
		'title' => $model->title
	);

	if (('flv' == $model->format) || ('mp4' == $model->format)) {
		$kapenta->raiseEvent('images', 'extract_video_thumb', $detail);
	}

}

?>
