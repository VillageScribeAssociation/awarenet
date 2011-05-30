<?

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	admin daily cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function admin_cron_daily() {
	global $db, $kapenta;
	$report = "<h2>admin_cron_daily</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	rotate the cron log
	//----------------------------------------------------------------------------------------------

	if (true == $kapenta->fileExists('data/cronlog.old.html')) {
		unlink($kapenta->installPath . 'data/cronlog.old.html');
		$report .= "[i] Removed yesterday's cron log.<br/>\n";
	}
	
	if (true == $kapenta->fileExists('data/cronlog.html')) {
		$oldFile = $kapenta->installPath . 'data/cronlog.html';
		$newFile = $kapenta->installPath . 'data/cronlog.old.html';
		$report .= "[i] Backed up and cleared today's cron log.<br/>\n";
		@copy($oldFile, $newFile);
		unlink($oldFile);
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

?>
