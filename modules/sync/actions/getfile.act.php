<?

//-------------------------------------------------------------------------------------------------
//*	used by peers to request another file from this server
//-------------------------------------------------------------------------------------------------
//	note: file path is base64 encoded string, which may only be within /data/
//	TODO: send hash along with file


	if ($sync->authenticate() == false) { $page->doXmlError('no authorized'); }
	if (array_key_exists('file', $req->args) == false) { $page->doXmlError('no file specified'); }
	if ('' == $req->args['file']) { $page->doXmlError('no file specified'); }

	//---------------------------------------------------------------------------------------------
	//	check filename and auth, etc
	//---------------------------------------------------------------------------------------------
	$relFile = base64_decode($req->args['file']);
	if (substr($relFile, 0, 5) != 'data/') { $page->doXmlError('invalid file'); }

	// check for directory traversal
	if (strpos($relFile, "/..") != false) { $page->doXmlError('invalid file'); }

	//---------------------------------------------------------------------------------------------
	// check if file exists on this peer
	//---------------------------------------------------------------------------------------------
	if (file_exists($kapenta->installPath . $relFile) == false) { $page->doXmlError('no such file'); }

	//---------------------------------------------------------------------------------------------
	//	send the file, along with sha1, to check it on reciept
	//---------------------------------------------------------------------------------------------

	$fileHash = sha1_file($kapenta->installPath . $relFile);
	header("Content-type: binary/sync");
 	header("Sha1-hash: " . $fileHash ); 
	readfile($kapenta->installPath . $relFile);

?>
