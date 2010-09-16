<?

	require_once($kapenta->installPath . 'modules/sync/models/download.mod.php');

//-------------------------------------------------------------------------------------------------
//	looks for a file among peers and downloads it if found
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	load the download record and set status to 'searching'
	//---------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->doXmlError('Download not specified.'); }
	if (false == $db->objectExists('downloads', $req->ref)) { $page->doXmlError('no such download'); }	

	$model = new Sync_Download($req->ref);
	if ($model->status != 'wait') { $page->doXmlError('busy'); }
	if (true == $model->maxDownloads()) { $page->doXmlError('Already downloading max files.'); }

	$model->status = 'searching';
	$model->save();

	$kapenta->logSync("searching for file " . $model->filename . "\n");

	$downloadComplete = false;

	//---------------------------------------------------------------------------------------------
	//	check all our peers, maybe they have the file
	//---------------------------------------------------------------------------------------------
	$peers = $sync->listPeers();
	foreach($peers as $peer) {

		$kapenta->logSync("peer " . $peer['UID'] . " serverurl " . $peer['serverurl'] . "\n");

		$testUrl = $peer['serverurl'] . 'sync/hasfile/file_'
				 . base64_encode($model->filename) . '/';

		$result = $sync->curlGet($testUrl, $peer['password']);

		$kapenta->logSync("findfile peer result ($testUrl) \n$result\n");

		$found = false;
		if (strpos($result, '</result>') > 0) { $found = true; }	// not an error
		if (strpos($result, 'not found') > 0) { $found = false; }	

		if (true == $found) {
			//-------------------------------------------------------------------------------------
			//	peer has the file we're looking for, download it
			//-------------------------------------------------------------------------------------
			$success = false;

			$hash = trim(strip_tags($result));
			$model->hash = $hash;
			$model->save();

			$kapenta->logSync("found file " . $model->filename . " (hash: " . $hash . ")\n");

			$getUrl = $peer['serverurl'] . 'sync/getfile/file_' 
					. base64_encode($model->filename) . '/';

			$result = $sync->curlGet($getUrl, $peer['password']);
			$rHash = trim(sha1($result));

			$kapenta->logSync("downloaded file $getUrl \n(" . strlen($result) . " bytes)(hash: $hash)\n");
						
			if (trim($rHash) == trim($hash)) {
				//---------------------------------------------------------------------------------
				//	file has come through correctly
				//---------------------------------------------------------------------------------
				$kapenta->logSync("hashes match, saving file " . $model->filename . "\n");
				$kapenta->filePutContents($model->filename, $result, true);
				$model->delete();	// done with this
				$downloadComplete = true;
				break;

			} else {
				//---------------------------------------------------------------------------------
				//	file has NOT come through correctly
				//---------------------------------------------------------------------------------
				$kapenta->logSync("hashes do not match, discarding download\n$hash != " . $rHash . "\n");

			}

		}
	}

	//---------------------------------------------------------------------------------------------
	//	file not found, set status back to wait
	//---------------------------------------------------------------------------------------------
	if (false == $downloadComplete) {
		$kapenta->logSync("file not sound, adding back to queue\n");
		$model->status = 'wait';
		$model->save();

	} else {
		//-----------------------------------------------------------------------------------------
		//	try for next file only if the last one worked (sync will try it again before long)
		//-----------------------------------------------------------------------------------------
		$nextDownload = $model->getNextDownload();
		if (false == $nextDownload) {
			$kapenta->logSync("all downloads completed\n");
		} else {
			$ofn = $kapenta->installPath . 'data/temp/' . $kapenta->createUID() . '.sync'
			$od = '--output-document=' . $ofn;
			$cmd = 'wget ' . $od . ' ' . $kapenta->serverPath . 'sync/findfile/' . $nextDownload;
			$kapenta->logSync("processing next download: $nextDownload\n$cmd\n");
			$kapenta->procExecBackground($cmd);
		}

	}
?>
