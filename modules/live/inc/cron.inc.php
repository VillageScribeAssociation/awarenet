<?

	require_once($kapenta->installPath . 'modules/live/models/mailbox.mod.php');
	require_once($kapenta->installPath . 'modules/live/inc/upload.class.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function live_cron_tenmins() {
	global $kapenta;
	global $db;

	$report = "<h2>live_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove any mailboxes which have not been checked in 10 mins or more
	//----------------------------------------------------------------------------------------------

	$expired = ($kapenta->time() - (60 * 10));
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


//--------------------------------------------------------------------------------------------------
//|	daily minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function live_cron_daily() {
	global $kapenta;
	global $db;

	$maxAge = 60 * 60 * 24 * 7;					//%	one week (seconds), TODO: registry setting [int]
	$report = "<h2>live_cron_daily</h2>\n";		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove any file uploads which have not completed after a week
	//----------------------------------------------------------------------------------------------
	$files = array();
	$list = $kapenta->fileList('data/live/uploads/', '.xxx');
	foreach($list as $fileName) { $files[] = $fileName; }
	$list = $kapenta->fileList('data/live/uploads/', '.xml.php');
	foreach($list as $fileName) { $files[] = $fileName; }

	foreach($files as $fileName) {
		$hash = str_replace(array('.xxx', 'data/live/uploads/'), array('', ''), $fileName);
		$report .= "checking: " . $fileName . " ($hash)<br/>\n";
		$model = new Live_Upload($hash);
		if (true == $model->loaded) {
			
			$age = $kapenta->time() - $kapenta->strtotime($model->started);	//%	seconds [int]
			if ($age > $maxAge) { 
				$report .= "Upload exceeds max age (" . $model->started . "), deleting...<br/>";
				
				foreach($model->parts as $part) {
					if (true == $kapenta->fileExists($part['fileName'])) {
						echo "Removing file part: " . $part['fileName'] . "<br/>";

					}
				}

			}

		} else {
			$report .= "[*] could not load: $hash<br/>\n";
			$report .= "[i] removing spurious file: $fileName<br/>\n";
			$check = $kapenta->fileDelete($fileName, true);
			if (false == $check) { $report .= "[*] Could not delete: $fileName<br/>\n"; }
			else { $report .= "[i] removed...<br/>\n"; }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
