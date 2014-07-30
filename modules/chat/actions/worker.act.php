<?

	require_once($kapenta->installPath . 'modules/chat/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	chat worker thread - handles asynchronous chat communications in the background
//--------------------------------------------------------------------------------------------------
//+	note that this action must be called regularly (~every 10 minutes) in order for the chat
//+	to work.

	$maxAge = 900;									//%	maximum age of a worker thread

//TODO: make maxage a registry setting - will be limited by max_execution_time php variable

	//----------------------------------------------------------------------------------------------
	//	check environment for other worker threads (there can be only one)
	//----------------------------------------------------------------------------------------------
	$pUID = $kapenta->createUID();					//%	UID of this worker process [string]
	$started = $kapenta->time();						//%	timestamp [int]

	$lastStarted = $kapenta->registry->get('chat.started');
	$lastWorker = $kapenta->registry->get('chat.worker');

	if ('' == $lastWorker) { $lastStarted = 0; }

	if ('' != $lastStarted) {
		if (($started - (int)$lastStarted) > $maxAge) {
			// previous worker expired or died without resigning, continue

		} else {
			// a worker already exists, not timed out
			echo "*** another worker thread already exists (" . $kapenta->datetime($lastStarted) . ")\n";
			die();

		}
	}
	
	$kapenta->registry->set('chat.worker', $pUID);
	$kapenta->registry->set('chat.started', $started);
	echo "** registered worker $pUID at $started (" . $kapenta->datetime($started) . ") **<br/>\n";
	flush();

	//----------------------------------------------------------------------------------------------
	//	continue polling server until timeout
	//----------------------------------------------------------------------------------------------
	
	while(($kapenta->time() - $started) < $maxAge) {
		echo "** cycle **<br/>\n";
		//echo "ktime: " . $kapenta->time() . " started: $started maxAge: $maxAge<br/>\n";
		echo ''
		 .  "diff: " . ($kapenta->time() - $started)
		 .  " ttl: " . ($maxAge - ($kapenta->time() - $started)) . "<br/>";
		flush();

		$client = new Chat_Client();
		$report = $client->check();

		echo "<pre>\n";
		echo $report;
		echo "</pre>\n";
		flush();
		sleep(3);
	}

	//----------------------------------------------------------------------------------------------
	//	done, resign as current worker thread
	//----------------------------------------------------------------------------------------------
	echo "** end of worker life, process resigning **<br/>"; flush();
	if ($pUID == $kapenta->registry->get('chat.worker')) { $kapenta->registry->set('chat.worker', ''); }

?>
