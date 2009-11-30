<?

//-------------------------------------------------------------------------------------------------
//	perform some action regularly (ie, no more than every 5 minutes)
//-------------------------------------------------------------------------------------------------

$cronInterval = 60;	// one minute
cronTest();

function cronTest() {
	global $installPath;
	global $cronInterval;

	$fileName = $installPath . 'core/lastcycle.txt';
	if (file_exists($fileName) == false) { filePutContents($fileName, time(), 'w+'); }
	$lastTime = implode(file($fileName));

	//---------------------------------------------------------------------------------------------
	//	run module cron scripts if enough time has elapsed
	//---------------------------------------------------------------------------------------------
	if (time() > ($lastTime + $cronInterval)) {
		filePutContents($fileName, time(), 'w+');	// set lastcycle to current time

		$mods = listModules();
		foreach($mods as $mod) {
			$incFile = $installPath . 'modules/' . $mod . '/inc/cron.inc.php';
			if (file_exists($incFile) == true) {
				require_once($incFile);
				$cronFn = $mod . '_cron';
				if (function_exists($cronFn) == true) { $cronFn(); }
			} // end if file exists
		} // end foreach mod
	} // end if time
}

?>
