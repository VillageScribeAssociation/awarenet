<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page for downloading files and associating them with records
//--------------------------------------------------------------------------------------------------
//TODO: improve and document this

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; 
	$refModel = '';
	$refUID = '';
	$return = '';
	$URL = '';

	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('refModule', $_POST)) { $refModule = $_POST['refModule']; }
	if (true == array_key_exists('refModel', $_POST)) { $refModel = $_POST['refModel']; }
	if (true == array_key_exists('refUID', $_POST)) { $refUID = $_POST['refUID']; }
	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }
	if (true == array_key_exists('URL', $_POST)) { $URL = $_POST['URL']; }
	
	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	
	if (('' == $refUID) OR ('' == $refModel) OR ('' == $refModule)) 
			{ $msg = "(missing arguments to file download)"; }

	if (('' == $msg) AND (false == $user->authHas($refModule, $refModel, 'files-add', $refUID))) 
			{ $msg = "(not authorised)";  }

	//----------------------------------------------------------------------------------------------
	//	download the file
	//----------------------------------------------------------------------------------------------
	//echo "downloading URL: $URL <br/>\n"; flush();
	if ('' == $msg) {
		$raw = implode(file($URL));
		if (false == $raw) { $msg = "file could not be downloaded, check the URL?"; }
	}

	//----------------------------------------------------------------------------------------------
	//	get file name
	//----------------------------------------------------------------------------------------------
	if ('' == $msg) {
		$fName = strtolower(basename($URL));
		if ($fName == '') { $imgName = $kapenta->createUID() . '.xxx'; }
	}

	//----------------------------------------------------------------------------------------------
	//	create file record and save file
	//----------------------------------------------------------------------------------------------
	if ('' == $msg) {
		echo "saving record<br/>\n";
		$model = new Files_File();
		$model->refModule = $refModule;
		$model->refModule = $refModel;
		$model->refUID = $refUID;
		$model->title = $fName;
		$model->storeFile($raw);
		$model->licence = 'unknown';
		$model->attribURL = $URL;
		$model->weight = '0';
		$model->save();
		$msg = "Downloaded file: $URL <br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
	
	//echo "message: " . $msg . "<br/>\n";
	
	$session->msg($msg);
	if ('uploadmultiple' == $return) { 
		$retURL = 'files/uploadmultiple'
			 . '/refModule_' . $refModule 
			 . '/refModel_' . $refModel 
			 . '/refUID_' . $refUID . '/'

		$page->do302($retURL);
	}

?>
