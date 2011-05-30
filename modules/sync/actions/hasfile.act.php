<?

	require_once($kapenta->installPath . 'modules/sync/models/download.mod.php');

//-------------------------------------------------------------------------------------------------
//*	discover if this peer has a file
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authenticate
	//---------------------------------------------------------------------------------------------
	if (false == $sync->authenticate()) { $page->doXmlError('could not authenticate'); }

	//---------------------------------------------------------------------------------------------
	//	check if the file exists
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('file', $req->args)) { $page->doXmlError('file not specified'); }
	if ($req->args['file'] == '') { $page->doXmlError('file not specified'); }

	$fileName = base64_decode($req->args['file']);
	if (substr($fileName, 0, 5) != 'data/')
		{ $page->doXmlError('access denied ref: ' . $req->args['file'] . ' filename: ' . $fileName); }

	$fileName = str_replace('/.', 'XXXX' , $fileName);
	
	if (true == file_exists($kapenta->installPath . $fileName)) {
		//-----------------------------------------------------------------------------------------
		//	file exists on this server
		//-----------------------------------------------------------------------------------------
		echo "<?xml version=\"1.0\"?>\n";
		$hash = sha1(implode(file($kapenta->installPath . $fileName)));
		echo "<result>$hash</result>";

	} else {
		//-----------------------------------------------------------------------------------------
		//	file file does not exist on this server, add to list of files to download
		//-----------------------------------------------------------------------------------------
		$model = new Sync_Download();
		if (false == $model->inList($fileName)) {
			$kapenta->logSync("hasfile: file does not exist in download list: $fileName download: " . $model->UID . "\n");
			$model->filename = $fileName;
			$model->status = 'wait';
			$model->save();
			
			//-------------------------------------------------------------------------------------
			//	look for this file on peers, download if found
			//-------------------------------------------------------------------------------------
			$url = $kapenta->serverPath . 'sync/findfile/' . $model->UID;
			$od = $kapenta->installPath . 'data/temp/' . $kapenta->createUID() . '.sync';
			$kapenta->procExecBackground("wget --output-document=" . $od . " $url");

		} else {
			//-------------------------------------------------------------------------------------
			//	already searching peers
			//-------------------------------------------------------------------------------------
			$kapenta->logSync("hasfile: file already exists in download list: $fileName \n");

		}

		echo "<result>not found</result>";
	}

?>
