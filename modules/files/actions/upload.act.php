<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page for accepting upload of files and associating them with records
//--------------------------------------------------------------------------------------------------
//TODO: improve and document this

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = '';
	$refModel = '';
	$refUID = '';
	$return = '';
	$tempFile = '';
	$srcName = '';

	$msg = '';
	$raw = '';
	$fName = '';

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $_POST)) { $kapenta->page->do404('refModule not given', true); }
	if (false == array_key_exists('refModel', $_POST)) { $kapenta->page->do404('refModel not given', true); }
	if (false == array_key_exists('refUID', $_POST)) { $kapenta->page->do404('refUID not given', true); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];

	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

	//----------------------------------------------------------------------------------------------
	//	security and validation
	//----------------------------------------------------------------------------------------------
	if (('' == $refUID) OR ('' == $refModule)) { $msg = "(missing arguments to file download)"; }
	if (('' == $msg) AND (false == $kapenta->user->authHas($refModule, $refModel, 'files-add', $refUID)))
		{ $msg = "(not authorised)";  }

	//----------------------------------------------------------------------------------------------
	//	get the upload
	//----------------------------------------------------------------------------------------------
	if (('' == $msg) AND (true == array_key_exists('userfile', $_FILES))) {
	
		$tempFile = $_FILES['userfile']['tmp_name'];
		$srcName = $_FILES['userfile']['name'];
		
		if (($srcName != '') AND (file_exists($tempFile))) { $raw = implode(file($tempFile)); }
		else { $msg = '(no file uploaded)'; }
		
	} else { $msg = '(no file uploaded)'; }
		
	//----------------------------------------------------------------------------------------------
	//	get file name
	//----------------------------------------------------------------------------------------------
	if ('' == $msg) {
		$fName = strtolower($srcName);
		if ('' == $fName) { $fName = $kapenta->createUID() . '.xxx'; }
	}

	//----------------------------------------------------------------------------------------------
	//	create file record and save file
	//----------------------------------------------------------------------------------------------
	if ('' == $msg) {
		$model = new Files_File();
		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->title = $fName;
		$model->storeFile($raw);
		$model->licence = 'unknown';
		$model->attribURL = '';
		$model->weight = '0';
		$model->save();
		$msg = "Uploaded file: $srcName <br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
	//TODO: more security checks here
	
	$kapenta->session->msg($msg);
	if ($return = 'uploadmultiple') {

		$retUrl = 'files/uploadmultiple'
			 . '/refModule_' . $refModule
			 . '/refModel_' . $refModel
			 . '/refUID_' . $refUID . '/';

		$kapenta->page->do302($retUrl);
	}

?>
