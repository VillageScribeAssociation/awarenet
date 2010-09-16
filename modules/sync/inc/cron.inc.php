<?

	require_once($kapenta->installPath . 'modules/sync/models/download.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//-------------------------------------------------------------------------------------------------
//*	periodic maintenance of sync
//-------------------------------------------------------------------------------------------------
//TODO: this can be much tidier

function sync_cron() {
	global $db, $req, $kapenta;

	//---------------------------------------------------------------------------------------------
	//	clear dead wood from downloads table
	//---------------------------------------------------------------------------------------------
	if (true == $db->tableExists('downloads')) {
		$model = new Sync_Download($req->ref);
		$model->clearOldEntries();
	}

	//---------------------------------------------------------------------------------------------
	//	retry failed sync items
	//---------------------------------------------------------------------------------------------
	$sql = "select * from Sync_Notice where status='failed'";	
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$od = $installPath . 'data/temp/' . time() . '-' . $row['UID'] . '.sync';
		$url = $serverPath . 'sync/send/' . $row['UID'];
		$shellCmd = "wget --output-document=" . $od . " $url";
		$kapenta->procExecBackground($shellCmd);
	}

	//---------------------------------------------------------------------------------------------
	//	look for wating downloads
	//---------------------------------------------------------------------------------------------
	$dl = new Sync_Download();
	for ($i = 0; $i < $dl->numDownloads; $i++) {
		$nextDl = $dl->getNextDownload();
		if  (($nextDl != false) && (false == $dl->maxDownloads()false)) {
			$od = '--output-document=' . $installPath . 'data/temp/' . $kapenta->createUID() . '.sync';
			$cmd = 'wget ' . $od . ' ' . $serverPath . 'sync/findfile/' . $nextDl;
			$kapenta->procExecBackground($cmd);	
		}
	}

	//---------------------------------------------------------------------------------------------
	//	delete crap from temp directory
	//---------------------------------------------------------------------------------------------
	$shellCmd = "rm " . $installPath . "data/temp/*.sync";	//TODO: fix this, remove shell_exec
	shell_exec($shellCmd);

}

?>
