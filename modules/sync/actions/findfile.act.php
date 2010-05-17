<?

//-------------------------------------------------------------------------------------------------
//	looks for a file among peers and downloads it if found
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/sync/models/download.mod.php');

	//---------------------------------------------------------------------------------------------
	//	load the download record and set status to 'searching'
	//---------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { doXmlError('download not specified'); }
	if (dbRecordExists('downloads', $request['ref']) == false) { doXmlError('no such download'); }	

	$model = new Download($request['ref']);
	if ($model->data['status'] != 'wait') { doXmlError('busy'); }
	if ($model->maxDownloads() == true) { doXmlError('already downloading max files'); }

	$model->data['status'] = 'searching';
	$model->save();

	logSync("searching for file " . $model->data['filename'] . "\n");

	$downloadComplete = false;

	//---------------------------------------------------------------------------------------------
	//	check all our peers, maybe they have the file
	//---------------------------------------------------------------------------------------------
	$peers = syncListPeers();
	foreach($peers as $peer) {

		logSync("peer " . $peer['UID'] . " serverurl " . $peer['serverurl'] . "\n");

		$testUrl = $peer['serverurl'] . 'sync/hasfile/file_'
				 . base64_encode($model->data['filename']) . '/';

		$result = syncCurlGet($testUrl, $peer['password']);

		logSync("findfile peer result ($testUrl) \n$result\n");

		$found = false;
		if (strpos($result, '</result>') > 0) { $found = true; }	// not an error
		if (strpos($result, 'not found') > 0) { $found = false; }	

		if (true == $found) {
			//-------------------------------------------------------------------------------------
			//	peer has the file we're looking for, download it
			//-------------------------------------------------------------------------------------
			$success = false;

			$hash = trim(strip_tags($result));
			$model->data['hash'] = $hash;
			$model->save();

			logSync("found file " . $model->data['filename'] . " (hash: " . $hash . ")\n");

			$getUrl = $peer['serverurl'] . 'sync/getfile/file_' 
					. base64_encode($model->data['filename']) . '/';

			$result = syncCurlGet($getUrl, $peer['password']);
			$rHash = trim(sha1($result));

			logSync("downloaded file $getUrl \n(" . strlen($result) . " bytes)(hash: $hash)\n");
						
			if (trim($rHash) == trim($hash)) {
				//---------------------------------------------------------------------------------
				//	file has come through correctly
				//---------------------------------------------------------------------------------
				logSync("hashes match, saving file " . $model->data['filename'] . "\n");
				filePutContents($installPath . $model->data['filename'], $result, 'w+');
				$model->delete();	// done with this
				$downloadComplete = true;
				break;

			} else {
				//---------------------------------------------------------------------------------
				//	file has NOT come through correctly
				//---------------------------------------------------------------------------------
				logSync("hashes do not match, discarding download\n$hash != " . $rHash . "\n");

			}

		}
	}

	//---------------------------------------------------------------------------------------------
	//	file not found, set status back to wait
	//---------------------------------------------------------------------------------------------
	if (false == $downloadComplete) {
		logSync("file not sound, adding back to queue\n");
		$model->data['status'] = 'wait';
		$model->save();
	} else {
		//-----------------------------------------------------------------------------------------
		//	try for next file only if the last one worked (sync will try it again before long)
		//-----------------------------------------------------------------------------------------
		$nextDownload = $model->getNextDownload();
		if (false == $nextDownload) {
			logSync("all downloads completed\n");
		} else {
			$od = '--output-document=' . $installPath . 'data/temp/' . createUID() . '.sync';
			$cmd = 'wget ' . $od . ' ' . $serverPath . 'sync/findfile/' . $nextDownload;
			logSync("processing next download: $nextDownload\n$cmd\n");
			procExecBackground($cmd);
		}
	}
?>
