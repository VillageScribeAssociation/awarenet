<?

	require_once($kapenta->installPath . 'modules/live/inc/upload.class.php');

//--------------------------------------------------------------------------------------------------
//*	called by JS file uploader when all parts are sent
//--------------------------------------------------------------------------------------------------
//postarg: filehash - sha1 hash of all file part hashes [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('filehash', $_POST)) { $page->doXmlError('File hash not given'); }
	
	$errmsg = '';

	//$session->msgAdmin('Calling uploadcomplete for: ' . $_POST['filehash']);

	//----------------------------------------------------------------------------------------------
	//	load upload manifest and stitch file parts together
	//----------------------------------------------------------------------------------------------
	$upload = new Live_Upload($_POST['filehash']);
	if (false == $upload->loaded) { $page->doXmlError('Manifest not found.'); }
	
	$check = $upload->stitchTogether();
	if (false == $check) { $page->doXmlError('Could not stitch file together.'); }

	//----------------------------------------------------------------------------------------------
	//	get extension and discover which module handles files of this type
	//----------------------------------------------------------------------------------------------
	$reg = $kapenta->registry->search('live', 'live.file.');

	foreach($reg as $key => $value) {
		$ext = str_replace('live.file.', '', $key);
		$compare = substr($upload->name, strlen($upload->name) - strlen($ext));

		//	special case for lessons module
		if ('lessons' == $upload->refModule) {
			$module = 'lessons';
			$session->msgAdmin('Assigning file to lessons module (ext: ' . $extension . ').');
			break;
		}

		//	or get file type from registry
		if (strtolower($ext) == strtolower($compare)) {
			$module = $value;
			$extension = $ext;
			$session->msgAdmin("/uploadcomplete/ module: $module ext: $extension upload: " . $upload->name);
			break;
		}
	}

	if ('' == $module) {
		$errmsg .= "Files of this type are not supported by %%websiteName%%.<br/>$srcName";
	}

	//----------------------------------------------------------------------------------------------
	//	get extension and discover which module handles files of this type
	//----------------------------------------------------------------------------------------------

	$args = array(
		'module' => $module,
		'refModule' => $upload->refModule,
		'refModel' => $upload->refModel,
		'refUID' => $upload->refUID,
		'name' => $upload->name,
		'srcName' => $upload->name,
		'path' => $upload->outFile,
		'type' => $upload->fileType,
		'extension' => $ext
	);

	$msg = ''
	 . "module => $module,<br/>\n"
	 . "refModule => " . $upload->refModule . ",<br/>\n"
	 . "refModel => " . $upload->refModel . ",<br/>\n"
	 . "refUID => " . $upload->refUID . ",<br/>\n"
	 . "name => " . $upload->name . ",<br/>\n"
	 . "path => " . $upload->outFile . ",<br/>\n"
	 . "type => " . $upload->fileType . ",<br/>\n"
	 . "extension => $ext<br/>\n";

	$session->msgAdmin($msg);

	$kapenta->raiseEvent($module, 'file_attach', $args);
	
	//----------------------------------------------------------------------------------------------
	//	delete the parts, the stitched file and the manifest
	//----------------------------------------------------------------------------------------------
	$upload->delete();
	echo "<ok/>";

?>
