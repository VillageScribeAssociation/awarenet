<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page for downloading files and associating them with records
//--------------------------------------------------------------------------------------------------
//TODO: improve and document this

	//----------------------------------------------------------------------------------------------
	//	control variables
	//----------------------------------------------------------------------------------------------
	$refModule = ''; 			//%	name of a kapenta module [string]
	$refModel = '';				//%	type of object which will own the file [string]
	$refUID = '';				//%	UID of object which will own the file [string]
	$return = '';
	$URL = '';					//%	location of file on the web [string]

	$msg = '';
	$raw = '';
	$img = false;
	$imgName = '';

	//----------------------------------------------------------------------------------------------
	//	check POST vars
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $_POST)) { $page->do404('refModule not given', true); }
	if (false == array_key_exists('refModel', $_POST)) { $page->do404('refModel not given', true); }
	if (false == array_key_exists('refUID', $_POST)) { $page->do404('refUID not given', true); }
	if (false == array_key_exists('URL', $_POST)) { $page->do404('URL not given', true); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];
	$URL = $_POST['URL'];

	if (true == array_key_exists('return', $_POST)) { $return = $_POST['return']; }

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
	//TODO: use cURL if available
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
		//echo "saving record<br/>\n";
		$model = new Files_File();
		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->title = $fName;
		$model->storeFile($raw);
		$model->licence = 'unknown';
		$model->attribUrl = $URL;
		$model->weight = '0';
		$report = $model->save();
		if ('' == $report) { $msg = "Downloaded file: $URL <br/>\n"; }
		else { $session->msg('Could not store file object.'); }
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back 
	//----------------------------------------------------------------------------------------------
	
	$session->msg($msg);
	if ('uploadmultiple' == $return) { 
		$retURL = 'files/uploadmultiple'
			 . '/refModule_' . $refModule 
			 . '/refModel_' . $refModel 
			 . '/refUID_' . $refUID . '/';

		$page->do302($retURL);
	}

?>
