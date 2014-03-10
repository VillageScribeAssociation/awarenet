<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	test pull updates from a peer
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	if ('' == $kapenta->request->ref) { $kapenta->page->do404('peer not found'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->do403(); }

	header('Content-type: text/plain');
	$msg = $peer->getUpdates();

	if (0 == count($msg)) {
		echo "queue empty.\n";
		die();
	}

	echo $msg['message'] . "\n\n";

	echo "file: " . $msg['file'] . "\n";
	echo "base: " . basename($msg['file']) . "\n";
	echo "sig: " . basename($msg['signature']) . "\n";
	echo "raw: " . strlen($msg['raw']) . "\n";

	print_r($msg);

	if ('yes' == $msg['verified']) {

		echo "\n\nParsing file:\n";
		$updates = new P2P_Updates($peer->UID);
		$updates->explode($msg['message'], $msg['priority'], $peer->UID);

		echo "\n\nConfirming update:\n";
		$response = $peer->ackUpdates($msg['file']);

		echo "peer responds:\n$response\n";
	}

?>
