<?

	require_once($kapenta->installPath . 'modules/sync/models/download.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/sync/inc/sync.inc.php');

//-------------------------------------------------------------------------------------------------
//*	periodic maintenance of sync
//-------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	hourly cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function sync_cron_hourly() {
	global $db, $req, $kapenta;
	$report = '<h2>sync_cron_hourly<h2>\n';

	//---------------------------------------------------------------------------------------------
	//	delete all failed sync notices more than a day old
	//---------------------------------------------------------------------------------------------

	$sql = "select * from sync_notice where status='failed'";
	$result = $db->query($sql);
	$now = time();

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$tsDiff = $now - strtotime($row['createdOn']);
		if ($tsDiff > (60 * 60 * 24)) { 					// more than 24 hours old?
			$model = new Sync_Notice();						// instantiate as object
			$model->loadArray($row);						// ....
			$model->delete();								// and delete
		}
	}	

	/*

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
	$sql = "select * from sync_notice where status='failed'";	
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$od = $kapenta->installPath . 'data/temp/' . time() . '-' . $row['UID'] . '.sync';
		$url = $kapenta->serverPath . 'sync/send/' . $row['UID'];
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
			$od = '--output-document=' . $kapenta->installPath . 'data/temp/' 
				. $kapenta->createUID() . '.sync';
			$cmd = 'wget ' . $od . ' ' . $kapenta->serverPath . 'sync/findfile/' . $nextDl;
			$kapenta->procExecBackground($cmd);	
		}
	}

	//---------------------------------------------------------------------------------------------
	//	delete crap from temp directory
	//---------------------------------------------------------------------------------------------
	$shellCmd = "rm " . $kapenta->installPath . "data/temp/*.sync";	
	//TODO: fix this, remove shell_exec
	shell_exec($shellCmd);

	*/

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function sync_cron_daily() {
	global $db, $req, $kapenta;
	$report = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	sync with all upstream and downstream hosts
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "active='active'";
	$conditions[] = "(direction='upstream' OR direction='downstream')";

	$range = $db->loadRange('sync_server', '*', $conditions);
	foreach($range as $row) {
		$report .= sync_entireDatabase($row['UID']);
		$report .= sync_allFiles($row['UID']);
	}

	return $report;
}

?>
