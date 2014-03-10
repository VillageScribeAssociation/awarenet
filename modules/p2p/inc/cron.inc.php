<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]
//TODO: implement hours for which objects and files may be synced

function p2p_cron_tenmins() {
	global $db;
	global $kapenta;
	
	$report = "<h2>p2p_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	clear expired locks in event queue
	//----------------------------------------------------------------------------------------------
	
	$allLocks = $kapenta->fs->listDir('data/p2p/received/', '.lock');
	foreach($allLocks as $lockFile) {
		$datetime = $kapenta->fs->get($lockFile);
		if ('' == $datetime) {
			//	dud lock file?
			$kapenta->fileDelete($lockFile, true);
			$report .= "Removed invalid lock: $lockFile<br/>\n";
	
		} else {
			//	check expiry (one hour)
			$now = $kapenta->time();
			$lockTime = $kapenta->strtotime($datetime);
			if (($now - $lockTime) > 3600) {
				$kapenta->fileDelete($lockFile, true);
				$report .= "Removed expired lock: $lockFile<br/>\n";
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	daily cron
//--------------------------------------------------------------------------------------------------

function p2p_cron_daily() {
	global $db;
	global $kapenta;
	
	$report = "<h2>p2p_cron_daily</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	remove expired file manifests (files have a week to download)
	//----------------------------------------------------------------------------------------------
	
	$minTime = $kapenta->time() - (60 * 60 * 24 * 7);		//	one week ago [string]

	$allManifests = $kapenta->fs->listDir('data/p2p/transfer/meta/', '.xml.php');
	foreach($allManifests as $manifestFile) {
		echo $manifestFile . "<br/>\n";

		$klf = new KLargeFile();	
		$klf->metaFile = $manifestFile;
		$klf->loadMetaXml();

		if (0 == $klf->timestamp) {
			$report .= $manifestFile . "<br/>";
			$report .= "Invalid manifest (missing timestamp), deleting...<br/>\n";
			$klf->metaFile = $manifestFile;		//	force original name, whatever that was
			$klf->delete();
		} else {
			//$report .= $manifestFile . "<br/>";
			//$report .= "Timestamp present on manifest - " . $klf->timestamp . "<br/>\n";
		}

		if ($minTime > $klf->timestamp) {
			$report .= $manifestFile . "<br/>";
			$report .= "Manifest has expired, deleting...<br/>\n";
			$klf->metaFile = $manifestFile;		//	force original name, whatever that was
			$klf->delete();
		}

	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	return $report;
}

?>
