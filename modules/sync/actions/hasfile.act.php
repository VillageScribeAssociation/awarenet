<?

//-------------------------------------------------------------------------------------------------
//	discover if this peer has a file
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/downloads.mod.php');

	//---------------------------------------------------------------------------------------------
	//	authenticate
	//---------------------------------------------------------------------------------------------
	if (syncAuthenticate() == false) { doXmlError('could not authenticate'); }

	//---------------------------------------------------------------------------------------------
	//	check if the file exists
	//---------------------------------------------------------------------------------------------
	if (array_key_exists('file', $request['args']) == false) { doXmlError('file not specified'); }
	if ($request['args']['file'] == '') { doXmlError('file not specified'); }

	$fileName = base64_decode($request['args']['file']);
	if (substr($fileName, 0, 5) != 'data/') { doXmlError('access denied ref: ' . $request['args']['file'] . ' filename: ' . $fileName); }
	$fileName = str_replace('/.', 'XXXX' , $fileName);
	
	if (file_exists($installPath . $fileName) == true) {
		//-----------------------------------------------------------------------------------------
		//	file exists on this server
		//-----------------------------------------------------------------------------------------
		echo "<?xml version=\"1.0\"?>\n";
		$hash = sha1(implode(file($installPath . $fileName)));
		echo "<result>$hash</result>";

	} else {
		//-----------------------------------------------------------------------------------------
		//	file file does not exist on this server, add to list of files to download
		//-----------------------------------------------------------------------------------------
		$model = new Download();
		if ($model->inList($fileName) == false) {
			logSync("hasfile: file does not exist in download list: $fileName download: " . $model->data['UID'] . "\n");
			$model->data['filename'] = $fileName;
			$model->data['status'] = 'wait';
			$model->save();
			
			//-------------------------------------------------------------------------------------
			//	look for this file on peers, download if found
			//-------------------------------------------------------------------------------------
			$url = $serverPath . 'sync/findfile/' . $model->data['UID'];
			$od = $installPath . 'data/temp/' . createUID() . '.sync';
			procExecBackground("wget --output-document=" . $od . " $url");

		} else {
			//-------------------------------------------------------------------------------------
			//	already searching peers
			//-------------------------------------------------------------------------------------
			logSync("hasfile: file already exists in download list: $fileName \n");

		}

		echo "<result>not found</result>";
	}

?>
