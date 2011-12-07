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
	
	$upload = new Live_Upload($_POST['filehash']);
	if (false == $upload->loaded) { $page->doXmlError('Manifest not found.'); }

	//----------------------------------------------------------------------------------------------
	//	stitch file parts together and raise event
	//----------------------------------------------------------------------------------------------
	$upload->stitchTogether();

	$args = array(
		'refModule' => $upload->refModule,
		'refModel' => $upload->refModel,
		'refUID' => $upload->refUID,
		'name' => $upload->name,
		'path' => $upload->outFile,
		'type' => $upload->fileType,
		'extension' => $upload->extension
	);

	$kapenta->raiseEvent('*', 'file_uploaded', $args);
	
	//----------------------------------------------------------------------------------------------
	//	delete the parts, the stitched file and the manifest
	//----------------------------------------------------------------------------------------------
	$upload->delete();
	echo "<ok/>";

?>
