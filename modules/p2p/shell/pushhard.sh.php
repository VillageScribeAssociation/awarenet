<?php

	require_once('../../../shinit.php');

	echo $kapenta->installPath . "\n";

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/worker.class.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell script to repeatedly pull from a peer
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	/*

	$usage_notes = ''
	 . "Usage: shinit.php [peerUID]|[peerName]|[peerUrl]\n"
	 . "This script will repeatedly poll a peer for new objects.\n\n";

	if (1 == count($argv)) { echo $argv[1] . "\n"; }

	$range = $kapenta->db->loadRange('p2p_peer', '*');
	$peerUID = '';

	foreach($range as $item) {
		if ($item['UID'] == $argv[1]) { $peerUID = $item['UID']; }
 		if (strtolower($item['name']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		if (strtolower($item['url']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		$usage_notes .= "peer: " . $item['UID'] . ' - ' . $item['name'] . " - " . $item['url'] . "\n";
	}
	$usage_notes .= "\n\n";

	if ('' == $peerUID) { echo $usage_notes; die(); }

	$peer = new P2P_Peer($peerUID);
	if (false == $peer->loaded) { echo $usage_notes; die(); }

	*/

	$worker = new P2P_Worker(true);		//	true - make it chatty

	$continue = true;

	while (true == $continue) {
		$continue = $worker->push();
	}

	echo "All queues empty.\n";

?>
