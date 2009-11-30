<?

//-------------------------------------------------------------------------------------------------
//	used by peers to request another file from this server
//-------------------------------------------------------------------------------------------------
//	note: file path is base64 encoded string, which may only be within /data/


	if (syncAuthenticate() == false) { doXmlError('no authorized'); }
	if (array_key_exists('file', $request['args']) == false) { doXmlError('no file specified'); }
	if ('' == $request['args']['file']) { doXmlError('no file specified'); }

	//---------------------------------------------------------------------------------------------
	//	check filename and auth, etc
	//---------------------------------------------------------------------------------------------
	$relFile = base64_decode($request['args']['file']);
	if (substr($relFile, 0, 5) != 'data/') { doXmlError('invalid file'); }

	// check for directory traversal
	if (strpos($relFile, "/..") != false) { doXmlError('invalid file'); }

	//---------------------------------------------------------------------------------------------
	// check if file exists on this peer
	//---------------------------------------------------------------------------------------------
	if (file_exists($installPath . $relFile) == false) { doXmlError('no such file'); }

	//---------------------------------------------------------------------------------------------
	//	send the file, along with sha1, to check it on reciept
	//---------------------------------------------------------------------------------------------

	$fileHash = sha1_file($installPath . $relFile);
	header("Content-type: binary/sync");
 	header("Sha1-hash: " . $fileHash ); 
	readfile($installPath . $relFile);

?>
