<?

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/worker.class.php');

//--------------------------------------------------------------------------------------------------
//*	shell worker process
//--------------------------------------------------------------------------------------------------

	/* ------------------------------------------------------------------------------------------ */
	$print = true;

	if ($print) { echo "Starting worker process...\n"; }

	$worker = new P2P_Worker($print);

	$worker->work();

	while (true == $continue) {

		echo "Pushing to: " . $peer->name . "\n";
		$report = $client->push();

		echo $report;
		$limit--;

		if (0 == $limit) { $continue = false; }
		if (false !== strpos($report, "Peer reports no changes.")) { $continue = false; }
	}

?>
