<?

//--------------------------------------------------------------------------------------------------
//	page for accepting upload of files and associating them with records
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/files/models/files.mod.php');

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; $refUID = ''; $return = ''; $tempFile = ''; $srcName = '';
	if (array_key_exists('refModule', $_POST)) { $refModule = sqlMarkup($_POST['refModule']); }
	if (array_key_exists('refUID', $_POST)) { $refUID = sqlMarkup($_POST['refUID']); }
	if (array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = ''; $raw = ''; $fName = '';
	
	if (($refUID == '') OR ($refModule == '')) { $msg = "(missing arguments to file download)"; }
	if (($msg = '') AND (authHas($refModule, 'files', '') == false)) { $msg = "(not authorised)";  }

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------
	if (($msg == '') AND (array_key_exists('userfile', $_FILES))) {
	
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		
		if (($srcName != '') AND (file_exists($tempFile))) {
			$raw = implode(file($tempFile));
		} else {
			$msg = '(no file uploaded)';
		}
		
	} else { $msg = '(no file uploaded)'; }
		
	//----------------------------------------------------------------------------------------------
	//	get file name
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$fName = strtolower($srcName);
		if ($fName == '') { $fName = createUID() . '.xxx'; }
	}

	//----------------------------------------------------------------------------------------------
	//	create file record and save file
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$f = new file();
		$f->data['refUID'] = $refUID;
		$f->data['refModule'] = $refModule;
		$f->data['title'] = $fName;
		$f->storeFile($raw);
		$f->data['licence'] = 'unknown';
		$f->data['attribURL'] = $URL;
		$f->data['weight'] = '0';
		$f->save();
		$msg = "Uploaded file: $srcName <br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
	
	$_SESSION['sMessage'] .= $msg;
	if ($return = 'uploadmultiple') {
		do302('files/uploadmultiple/refModule_' . $refModule . '/refUID_' . $refUID . '/');
	}

?>
