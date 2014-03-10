<?

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------
//TODO: move this log functionality to core object
//TODO: registry settings to control this

//--------------------------------------------------------------------------------------------------
//|	admin daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function admin_cron_daily() {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	$report = "<h2>admin_cron_daily</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	rotate the logs
	//----------------------------------------------------------------------------------------------
	$tempLogs = array('p2p', 'cron', 'slow-views', 'views-slow', 'db-slow', 'page-slow');
	$files = $kapenta->listFiles('data/log/');
	$hostOs = $kapenta->registry->get('kapenta.os');

	foreach($files as $fileName) {
		if (false !== strpos($fileName, '.log.php')) {
			$logTime = log_get_timestamp($fileName);
			$now = $kapenta->time();
			$age = $now - $logTime;

			//--------------------------------------------------------------------------------------
			//	when logs are more than a week old...
			//--------------------------------------------------------------------------------------

			if ($age > 604800) {		//	~ one week

				//----------------------------------------------------------------------------------
				//	remove temporary / status logs
				//----------------------------------------------------------------------------------

				foreach($tempLogs as $find) {
					if (false !== strpos($fileName, '-' . $find . '.log.php')) {
						$report .= "Diagnostic log expired, removing: $fileName <br/>\n";
						$kapenta->fileDelete('data/log/' . $fileName, true);
					}
				}

				//----------------------------------------------------------------------------------
				//	compress the rest			//	TODO: compress files on windows
				//----------------------------------------------------------------------------------

				if (
					('linux' == $hostOs) &&
					(false == strpos($fileName, '.gz')) &&
					(true == $kapenta->fileIsExtantRW('data/log/' . $fileName))
				) {
					$report .= "Compressing log: " . $fileName . "<br/>\n";
					log_compress($fileName);
				}

			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//	utility methods
//--------------------------------------------------------------------------------------------------

function log_get_timestamp($fileName) {
	global $kapenta;
	$fileName = substr($fileName, 0, 8) . ' 00:00:00';
	return $kapenta->strtotime($fileName);
}

function log_compress($fileName) {
	global $kapenta;
	//TODO: sanitzation and checks here
	$shellCmd = "gzip '" . $kapenta->installPath . "data/log/" . $fileName . "'";
	//echo $shellCmd . "<br/>\n";
	shell_exec($shellCmd);
}

?>
