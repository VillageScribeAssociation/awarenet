<?

//--------------------------------------------------------------------------------------------------
//*	temporary action for fixing XML in page view logs
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	fix 'em
	//----------------------------------------------------------------------------------------------
	$logFiles = $kapenta->listFiles('data/log/', 'pageview.log.php');
	foreach($logFiles as $logFile) {
		$fileName = 'data/log/' . $logFile;
		echo $fileName . "<br/>\n";
		$raw = $kapenta->fileGetContents($fileName, true, false);
		$raw = str_replace('<timstamp>', '<timestamp>', $raw);
		$kapenta->filePutContents($fileName, $raw, false, false);
	}

?>
