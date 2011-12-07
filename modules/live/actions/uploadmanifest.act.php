<?

	require_once($kapenta->installPath . 'modules/live/inc/upload.class.php');

//--------------------------------------------------------------------------------------------------
//*	sent by clients uploading a large file
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'addManifest' [string]
//postarg: refModule - name of a kapenta module [string]
//postarg: refModel - type of object which may accpet large files [string]
//postarg: refUID - UID of object to attach files to [string]
//postarg: hash - hash of file (sha1 of all part hashes)  [string]
//postarg: manifest64 - base64 encoded manifest (xml) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->doXmlError('action not given.'); }
	if ('addManifest' != $_POST['action']) { $page->doXmlError('Unknown action.'); }

	if (false == array_key_exists('refModule', $_POST)) { $page->doXmlError('refModule not given'); }
	if (false == array_key_exists('refModel', $_POST)) { $page->doXmlError('refModel not given'); }
	if (false == array_key_exists('refUID', $_POST)) { $page->doXmlError('refUID not given'); }
	if (false == array_key_exists('hash', $_POST)) { $page->doXmlError('hash not given'); }
	if (false == array_key_exists('manifest64', $_POST)) { $page->doXmlError('manifest not given'); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];
	$hash = $_POST['hash'];

	if (false == $kapenta->moduleExists($refModule)) { $page->doXmlError('unknown module'); }
	//if (false == $user->authHas($refModule, $refModel, 'upload-add', $refUID)) { 
	//	$page->doXmlError('You are not permitted to attach files to this object.');
	//}

	//----------------------------------------------------------------------------------------------
	//	create / resume the upload
	//----------------------------------------------------------------------------------------------
	$hash = $_POST['hash'];					//TODO: better sanitization here
	$hash = str_replace('.', '', $hash);
	$hash = str_replace('/', '', $hash);
	$hash = str_replace('\\', '', $hash);

	$manifest64 = str_replace(' ', '+', $_POST['manifest64']);
	$xml = base64_decode($manifest64);

	$upload = new Live_Upload($hash);
	if (false == $upload->loaded) {	
		//------------------------------------------------------------------------------------------
		//	this is a new file, nobody is uploading it
		//------------------------------------------------------------------------------------------
		$upload->loadXml($xml);
		if (true == $upload->loaded) { $upload->saveXml(); }
		else { $page->doXmlError('Could not load manifest.'); }

	} else {
		//------------------------------------------------------------------------------------------
		//	somebody is already uploading this file
		//------------------------------------------------------------------------------------------
		$upload->updated = $kapenta->datetime();
		$upload->saveXml();
	}

	echo '<b>' . $upload->getBitmapTemp() . '</b>';

?>
