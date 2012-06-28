<?

//--------------------------------------------------------------------------------------------------
//*	action for accepting file uploads
//--------------------------------------------------------------------------------------------------
//+	We might consider raising the file_uploaded event for all modules.  An example use case is
//+	a virus scanner module which checks all files as they are uplaoded.  For this reason all
//+	event handlers which listen for 'file_uploaded' should check 'module' argument, and not assume
//+	that the file is meant for them.

//postarg: refModule - module of object which will own oploaded file [string]
//postarg: refModel - type of object which will own uploaded file [string]
//postarg: refUID - UID of object which will own uploaded file [string]
//postarg: module - name of module which handles files of this type [string]
//postopt: return - return type 'xml' for xml status message, url for redirect [string]
//postopt: hash - sha1 hash of uploaded file, if availble [string]

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------

	$refModule = ''; 				//%	owner object's module [string]
	$refModel = ''; 				//%	owner object's type [string]
	$refUID = ''; 					//%	owner object's UID [string]
	$module = '';					//%	module which handle uploaded file [string]
	$extension = '';				//%	extension of original file name [string]
	$return = '';					//%	redirect URL or 'xml' [string]
	$hash = '';						//%	sha1 hash of new file [string]

	$tempFile = ''; 				//%	uploaded file location [string]
	$srcName = '';					//%	original name of file as given by browser [string]
	$errmsg = '';

	if (false == array_key_exists('refModule', $_POST)) { $page->do404('refModule missing', true); }
	if (false == array_key_exists('refModel', $_POST)) { $page->do404('refModel missing', true); }
	if (false == array_key_exists('refUID', $_POST)) { $page->do404('refUID missing', true); }

	if (true == array_key_exists('module', $_POST)) { $module = $_POST['module']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (true == array_key_exists('hash', $_POST)) { $hash = $_POST['hash']; }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];

	//	temporary file to give to handler module
	$path = 'data/temp/' . time() . '_upload_' . $kapenta->createUID() . '.bin';

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$return = "live/attachments/refModule_$refModule/refModel_$refModel/refUID_$refUID/";

	if (false == $kapenta->moduleExists($refModule)) { $errmsg = 'Missing owner module.'; }
	if (false == $db->objectExists($refModel, $refUID)) { $errmsg = 'Owner object not found.'; }

	if (true == array_key_exists('userfile', $_FILES)) {
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];

		if (true == file_exists($tempFile)) {

			$realHash = sha1_file($tempFile);
			if ('' !== $hash) {
				if ($realHash !== $hash) { $errmsg = 'Upload broken (hash mismatch).'; }
			} else {
				$hash = $realHash;	//	no way to tell if broken, so assume all is OK
			}
			
		} else {
			$errmsg = 'No file uploaded.';
		}
	
	} else { 
		$errmsg = 'No file uploaded.'; 
	}

	//----------------------------------------------------------------------------------------------
	//	get extension and discover which module handles files of this type
	//----------------------------------------------------------------------------------------------
	if ('' == $errmsg) {
		$reg = $registry->search('live', 'live.file.');

		foreach($reg as $key => $value) {
			$ext = str_replace('live.file.', '', $key);
			$compare = substr($srcName, strlen($srcName) - strlen($ext));
			if (strtolower($ext) == strtolower($compare)) {
				$module = $value;
				$extension = $ext;
				$session->msgAdmin("/uploadcomplete/ module: $module ext: $extension upload: " . $srcName);
			}
		}

		if ('' == $module) {
			$errmsg .= "Files of this type are not supported by %%websiteName%%.<br/>$srcName";
		}

		if (false == $user->authHas($refModule, $refModel, $module . '-add', $refUID)) {
			$errmsg = 'You are not authorised to add ' . $module . 's to this item.';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	get extension and discover which module handles files of this type
	//----------------------------------------------------------------------------------------------
	if ('' == $errmsg) {
		$reg = $registry->search('live', 'live.file.');

		foreach($reg as $key => $value) {
			$ext = str_replace('live.file.', '', $key);
			$compare = substr($srcName, strlen($srcName) - strlen($ext));
			if (strtolower($ext) == strtolower($compare)) {
				$module = $value;
				$extension = $ext;
			}
		}

		if ('' == $module) {
			$errmsg .= "Files of this type are not supported by %%websiteName%%.<br/>$srcName";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	copy file to /data/temp/
	//----------------------------------------------------------------------------------------------

	if ('' == $errmsg) {
		copy($tempFile, $kapenta->installPath . $path);				//	absolute locations
		if (false == $kapenta->fileExists($path)) {
			$errmsg .= ''
			 . "Could not copy temp file.<br/>\n"
			 . "src: $tempFile<br/>\n"
			 . "dest: " . $kapenta->installPath . 'data/temp/' . $path;
		}
	}		

	//----------------------------------------------------------------------------------------------
	//	raise file_uploaded event on owner module
	//----------------------------------------------------------------------------------------------
	//	note that owner module must do something with this file, the temp file will be deleted
	//	immediately.

	if ('' == $errmsg) {
		$args = array(
			'module' => $module,
			'refModule' => $refModule,
			'refModel' => $refModel,
			'refUID' => $refUID,
			'path' => $path,
			'srcName' => $srcName,
			'name' => basename($srcName),
			'extension' => $extension,
			'hash' => $hash
		);

		$outcome = $kapenta->raiseEvent($module, 'file_attach', $args);
		foreach($outcome as $mod => $eventError) { $errmsg .= $eventError; }
	}

	//----------------------------------------------------------------------------------------------
	//	remove temp file
	//----------------------------------------------------------------------------------------------

	$kapenta->fileDelete($path);
	unlink($tempFile);

	//----------------------------------------------------------------------------------------------
	//	return xml or redirect back 
	//----------------------------------------------------------------------------------------------
	
	if ($return == 'xml') {
		if ('' !== $errmsg) { $page->doXmlError($errmsg); }
		echo "<ok/>";
		die();
	}

	if ('' == $errmsg) { $session->msg("Uploaded image: $srcName <br/>\n", 'ok'); }
	else { $session->msg($errmsg, 'bad'); }

	$page->do302($return);

?>
