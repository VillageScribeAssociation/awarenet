<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when a file has been uploaded by the live module
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which owns this image [string]
//arg: refUID - UID of object which own this image [string]
//arg: path - location of file [string]
//arg: srcName - original location of file [string]
//arg: name - original name file [string]
//arg: ext - file extension [string]
//arg: module - module which handles files of this type [string]

function files__cb_file_attach($args) {
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
	$session->msg($msg);

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('path', $args)) { return false; }

	if ('files' != $args['module']) { return false; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	create a new files_file object attached to this gallery
	//----------------------------------------------------------------------------------------------

	$model = new Files_File();
	$model->UID = $kapenta->createUID();
	$model->refModule = $args['refModule'];
	$model->refModel = $args['refModel'];
	$model->refUID = $args['refUID'];
	$model->title = $utils->cleanTitle($args['name']);
	$model->format = $args['extension'];
	$model->shared = 'yes';

	$model->fileName = ''
	 . 'data/files/'
	 . substr($model->UID, 0, 1) . '/'
	 . substr($model->UID, 1, 1) . '/'
	 . substr($model->UID, 2, 1) . '/'
	 . $model->UID . '.xxx';

	$model->fileSize = $kapenta->fileSha1($args['path']);
	$model->hash = $kapenta->fileSha1($args['path']);

	$report = $model->save();

	if ('' == $report) {
		$check = $kapenta->fileCopy($args['path'], $model->fileName);
		$session->msg('Attached file.');

		//------------------------------------------------------------------------------------------
		//	file was uploaded correctly raise file_added event for this image (p2p uses it)
		//------------------------------------------------------------------------------------------
		$detail = array(
			'refModule' => 'files', 
			'refModel' => 'files_file', 
			'refUID' => $model->UID, 
			'fileName' => $model->fileName, 
			'hash' => $model->hash,
			'size' => $model->fileSize
		);

		$kapenta->raiseEvent('*', 'file_added', $detail);

		//------------------------------------------------------------------------------------------
		//	send 'file_added' event to module whose record owns this file
		//------------------------------------------------------------------------------------------
		$detail = array(	
			'refModule' => $model->refModule, 
			'refModel' => $model->refModel, 
			'refUID' => $model->refUID, 
			'fileUID' => $model->UID, 
			'fileTitle' => $model->title    
		);

		$kapenta->raiseEvent('*', 'file_added', $detail);
		$session->msgAdmin('Attached file.');

	} else {
		$session->msg('Could not create file object.');
	}

}

?>
