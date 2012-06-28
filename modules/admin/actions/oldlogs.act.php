<?

//--------------------------------------------------------------------------------------------------
//*	find logs more than one month old (excluding pageview logs)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check req args and user role
	//----------------------------------------------------------------------------------------------
	
	//if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	go through logs
	//----------------------------------------------------------------------------------------------

	$list = $kapenta->fileList('data/log/', '.log.php');

	foreach($list as $item) {
		$date = '20' . substr($item, 9, 8);			//	date log was started
		$age = time() - strtotime($date);		//	age of log in seconds

		if ($age > (60 * 60 * 24 * 30)) {
			echo "$item ($date ~ $age) (OLD)<br/>\n";
			echo "compressing...<br/>";

			$shellCmd = "gzip \"" . $kapenta->installPath . $item . "\"";
			echo $shellCmd . "<br/>";
			shell_exec($shellCmd);
			flush();

		} else {
			echo "$item ($date ~ $age) (not old)<br/>\n";
		}
	}

?>
