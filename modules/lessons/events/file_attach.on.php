<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

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

function images__cb_file_attach($args) {
	global $kapenta;
	global $session;
	global $utils;
	global $kapenta;
	global $kapenta;

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

	if ('lessons' != $args['refModule']) { return false; }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	check file type
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load a course
	//----------------------------------------------------------------------------------------------

	$course = new Lessons_Collection($refUID);
	if (false == $model->loaded) {
		$kapenta->session->msg('Could not load course to attach file: ' . $refUID);
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	create a new sub attached to this course
	//----------------------------------------------------------------------------------------------

	$model = new Lessons_Stub();

	$model->course = $course->UID;
	$model->file = 'data/lessons/documents/' . $model->UID . '.' . $args['extension'];
	$model->title = basename($args['srcName']);
	$model->type = $args['extension'];
	$model->description = 'Added by ' . $kapenta->user->getName() . ' on ' . $kapenta->datetime();
	$model->attribname = $kapenta->user->getName();
	$model->attriburl = '%%serverPath%%users/profile/' . $kapenta->user->alias;
	$model->licencename = 'CC BY-NC';
	$model->lienceurl = 'http://www.creativecommons.org/';  // TODO: full URL of deed

	$kapenta->fileMakeSubdirs($model->fileName);

	$report = $model->save();

	if ('' == $report) {
		//------------------------------------------------------------------------------------------
		//	convert file to appropriate format
		//------------------------------------------------------------------------------------------
	}

	if ('' == $report) {

		//------------------------------------------------------------------------------------------
		//	file was added correctly - re-export the course manifest
		//------------------------------------------------------------------------------------------
		$detail = array(
			'refModule' => 'lessons', 
			'refModel' => 'lessons_collection', 
			'refUID' => $course->UID, 
			'fileName' => $model->file,
			'extension' => $args['ext']
		);

		$kapenta->raiseEvent('*', 'lessons_export', $detail);

		//------------------------------------------------------------------------------------------
		//	raise lessons_resource_added
		//------------------------------------------------------------------------------------------
		$detail = array(	
			'refModule' => $model->refModule, 
			'refModel' => $model->refModel, 
			'refUID' => $model->refUID, 
			'imageUID' => $model->UID, 
			'imageTitle' => $model->title    
		);

		$kapenta->raiseEvent('*', 'lessons_resource_added', $detail);
		$kapenta->session->msgAdmin('Attached image file.');

		//------------------------------------------------------------------------------------------
		//	tag the new image with the username and file name it was added by
		//------------------------------------------------------------------------------------------
		//TODO: consider how tagging will work with resources
		/*
		$detail = array(
			'refModule' => 'images',
			'refModel' => 'images_image',
			'refUID' => $model->UID
		);

		$detail['tagName'] = $kapenta->user->getName();
		$kapenta->raiseEvent('tags', 'tags_add', $detail);

		//$detail['tagName'] = $kapenta->user->username;
		//$kapenta->raiseEvent('tags', 'tags_add', $detail);
		*/

	} else {
		$kapenta->session->msg('Could not add file to course: ' . $report);
	}

}

?>
