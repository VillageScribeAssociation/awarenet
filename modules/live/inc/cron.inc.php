<?

	require_once($kapenta->installPath . 'modules/live/models/mailbox.mod.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function live_cron_tenmins() {
	global $kapenta, $db;
	$report = "<h2>live_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove any mailboxes which have not been checked in 10 mins or more
	//----------------------------------------------------------------------------------------------

	$expired = (time() - (60 * 10));
	$sql = "select * from live_mailbox where lastChecked < $expired ;";
	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$model = new Live_Mailbox();
		$model->loadArray($db->rmArray($row));
		$model->delete();

		$msg = "[i] Removed old mailbox (UID: ". $model->UID ." page: ". $model->pageUID ." )<br/>";
		$report .= $msg;
		$kapenta->logLive($msg);
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
