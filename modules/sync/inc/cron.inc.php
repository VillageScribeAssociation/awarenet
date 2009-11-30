<?

//-------------------------------------------------------------------------------------------------
//	periodic maintenance of sync
//-------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/sync/models/downloads.mod.php');
require_once($installPath . 'modules/sync/models/sync.mod.php');

function sync_cron() {
	global $serverPath;
	global $installPath;

	//---------------------------------------------------------------------------------------------
	//	clear dead wood from downloads table
	//---------------------------------------------------------------------------------------------
	if (dbTableExists('downloads') == true) {
		$model = new Download($request['ref']);
		$model->clearOldEntries();
	}


	//---------------------------------------------------------------------------------------------
	//	retry failed sync items
	//---------------------------------------------------------------------------------------------
	$sql = "select * from sync where status='files'";	
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$od = $installPath . 'data/temp/' . time() . '-' . $row['UID'] . '.sync';
		$url = $serverPath . 'sync/send/' . $row['UID'];
		$shellCmd = "wget --output-document=" . $od . " $url";
		procExecBackground($shellCmd);
	}


	//---------------------------------------------------------------------------------------------
	//	look for wating downloads
	//---------------------------------------------------------------------------------------------
	$dl = new Download();
	for ($i = 0; $i < $dl->numDownloads; $i++) {
		$nextDl = $dl->getNextDownload();
		if  (($nextDl != false) && ($dl->maxDownloads() == false)) {
			$od = '--output-document=' . $installPath . 'data/temp/' . createUID() . '.sync';
			$cmd = 'wget ' . $od . ' ' . $serverPath . 'sync/findfile/' . $nextDl;
			procExecBackground($cmd);	
		}
	}

	//---------------------------------------------------------------------------------------------
	//	delete crap from temp directory
	//---------------------------------------------------------------------------------------------
	$shellCmd = "rm " . $installPath . "data/temp/*.sync";
	shell_exec($shellCmd);

}

?>
