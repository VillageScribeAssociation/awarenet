<?

//--------------------------------------------------------------------------------------------------
//*	perform some action regularly (ie, no more than every 1 minutes)
//--------------------------------------------------------------------------------------------------
//+ This is likely to be moved to its own module and called by system cron via wget
//TODO: upgrade this to version 4, move it to the system object

cronTest();

function cronTest() {
	global $kapenta;
	global $cronInterval;

	$fileName = 'core/lastcycle.txt';		//%	location of cron data file [string]
	if (false == $kapenta->fileExists($fileName)) 
		{ $kapenta->filePutContents($fileName, time(), false, true); }

	$lastTime = $kapenta->fileGetContents($fileName, false, true);

	//----------------------------------------------------------------------------------------------
	//	run module cron scripts if enough time has elapsed
	//----------------------------------------------------------------------------------------------
	if (time() > ($lastTime + $cronInterval)) {
		$kapenta->filePutContents($fileName, time(), false, true);	// set lastcycle to current time

		$mods = $kapenta->listModules();
		foreach($mods as $mod) {
			$incFile = 'modules/' . $mod . '/inc/cron.inc.php';
			if (true == $kapenta->fileExists($incFile)) {
				require_once($incFile);
				$cronFn = $mod . '_cron';
				if (true == function_exists($cronFn)) { $cronFn(); }
			} // end if file exists
		} // end foreach mod
	} // end if time
}

?>
