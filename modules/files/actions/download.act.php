<?

//--------------------------------------------------------------------------------------------------
//	page for downloading files and associating them with records
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/files/models/file.mod.php');

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; $refUID = ''; $return = ''; $URL = '';
	if (array_key_exists('refModule', $_POST)) { $refModule = sqlMarkup($_POST['refModule']); }
	if (array_key_exists('refUID', $_POST)) { $refUID = sqlMarkup($_POST['refUID']); }
	if (array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (array_key_exists('URL', $_POST)) { $URL = $_POST['URL']; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	$msg = ''; $raw = ''; $img = false; $imgName = '';
	
	if (($refUID == '') OR ($refModule == '')) { $msg = "(missing arguments to file download)"; }
	if (($msg = '') AND (authHas($refModule, 'files', '') == false)) { $msg = "(not authorised)";  }

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------
	echo "downloading URL: $URL <br/>\n"; flush();
	if ($msg == '') {
		$raw = implode(file($URL));
		if ($raw == false) { $msg = "file could not be downloaded, check the URL?"; }
	}

	//----------------------------------------------------------------------------------------------
	//	get file name
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		$fName = strtolower(basename($URL));
		if ($fName == '') { $imgName = createUID() . '.xxx'; }
	}

	//----------------------------------------------------------------------------------------------
	//	create file record and save file
	//----------------------------------------------------------------------------------------------
	if ($msg == '') {
		echo "saving record<br/>\n";
		$f = new File();
		$f->data['refUID'] = $refUID;
		$f->data['refModule'] = $refModule;
		$f->data['title'] = $fName;
		$f->storeFile($img);
		$f->data['licence'] = 'unknown';
		$f->data['attribURL'] = $URL;
		$f->data['weight'] = '0';
		$f->save();
		$msg = "Downloaded file: $URL <br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
	
	//echo "message: " . $msg . "<br/>\n";
	
	$_SESSION['sMessage'] .= $msg;
	if ($return = 'uploadmultiple') {
		do302('files/uploadmultiple/refModule_' . $refModule . '/refUID_' . $refUID . '/');
	}

?>
