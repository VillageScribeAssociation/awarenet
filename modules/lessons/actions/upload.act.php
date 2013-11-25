<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');
	require_once($kapenta->installPath . 'modules/lessons/inc/covers.inc.php');

//--------------------------------------------------------------------------------------------------
//*	add a new document to a package 
//--------------------------------------------------------------------------------------------------
//postarg: manifestUID - UID of an installed course package [string]
//postarg: title - name of this document [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('manifestUID', $_POST)) { $page->do404('no manifest uid'); }
	if (false == array_key_exists('title', $_POST)) { 
		print_r($_POST);
		$page->do404('no title');
	}

	$model = new Lessons_Course($_POST['manifestUID']);
	if (false == $model->loaded) { $page->do404('Course not found.'); }

	//----------------------------------------------------------------------------------------------
	//	get document metadata
	//----------------------------------------------------------------------------------------------

	$newDoc = array();

	foreach($model->dProperties as $key => $value) {
		if (true == array_key_exists($key, $_POST)) {
			$newDoc[$key] = trim($_POST[$key]);
		}
	}

	if (false == array_key_exists('uid', $newDoc)) { $newDoc['uid'] = $kapenta->createUID(); }

	//----------------------------------------------------------------------------------------------
	//	add file
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('userfile', $_FILES)) {
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		$ext = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
		$ext = strtolower($ext);

		switch($ext) {
			case 'pdf':		break;
			case 'mp4':		break;
			case 'flv':		break;

			//TODO: covert word and powerpoint documents here

			default:
				$session->msg("Files of type $ext are not current supported by this server.", 'bad');
				$page->do302('lessons/editmanifest/' . $model->UID);
				break;
		}

		$destName = 'data/lessons/' . $model->UID . '/documents/' . $newDoc['uid'] . '.' . $ext;

		if (true == file_exists($tempFile)) {

			$realHash = sha1_file($tempFile);
			if ('' !== $hash) {
				if ($realHash !== $hash) { $errmsg = 'Upload broken (hash mismatch).'; }
			} else {
				$hash = $realHash;	//	no way to tell if broken, so assume all is OK
			}

			$session->msg("Temp file: $tempFile");
			$session->msg("Dest file: $destName");

			$kapenta->fileMakeSubdirs($destName);

			copy($tempFile, $kapenta->installPath . $destName);

			if (false == $kapenta->fileExists($destName)) {
				$session->msg("Error during file upload, please try again.", 'bad');
				$page->do302('lessons/editmanifest/' . $model->UID);				
			}

			$newDoc['file'] = $destName;
			$newDoc['type'] = $ext;
			$newDoc['cover'] = "data/lessons/{$model->UID}/covers/{$newDoc['uid']}.$ext.jpg";
			$newDoc['thumb'] = "data/lessons/{$model->UID}/thumbs/{$newDoc['uid']}.$ext.jpg";
			$kapenta->fileMakeSubdirs($newDoc['cover']);
			$kapenta->fileMakeSubdirs($newDoc['thumb']);

			lessons_extractImages($model->UID, $newDoc);

		} else {
			$page->do404('No file uploaded.');
		}
	
	} else { 
		$page->do404('No file uploaded.'); 
	}


	//----------------------------------------------------------------------------------------------
	//	save to disk
	//----------------------------------------------------------------------------------------------

	$model->documents[] = $newDoc;
	$check = $model->save();

	if (true == $check) {
		$page->do302('lessons/editmanifest/' . $model->UID);
	} else {
		echo "<textarea rows='20' cols='100'>" . $model->toXML() . "</textarea>";
	}

	//----------------------------------------------------------------------------------------------
	//	rebuld package lists
	//----------------------------------------------------------------------------------------------


?>
