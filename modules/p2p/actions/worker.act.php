<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/worker.class.php');

//--------------------------------------------------------------------------------------------------
//*	worker action which runs after cron (singleton)
//--------------------------------------------------------------------------------------------------

	@header('Content-type: text/plain');

	$print = true;

	if ($print) { echo "Starting worker process...\n"; flush(); }

	$worker = new P2P_Worker($print);

	$worker->work();

	while (true == $continue) {

		echo "Pushing to: " . $peer->name . "\n"; flush();
		$report = $client->push();

		echo $report; flush();
		$limit--;

		if (0 == $limit) { $continue = false; }
		if (false !== strpos($report, "Peer reports no changes.")) { $continue = false; }
	}

?>
