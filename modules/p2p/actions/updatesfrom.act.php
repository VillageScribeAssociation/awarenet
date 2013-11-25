<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	receive updates from a peer by HTTP POST
//--------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $page->doXmlError('peer not specified.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $page->doXmlError('unknown peer'); }

	if (false == array_key_exists('m', $_POST)) { $page->doXmlError('no message'); }
	$msg = $peer->unpack($_POST['m']);

	echo "file: " . $msg['file'] . "\n";
	//echo "base: " . basename($msg['file']) . "\n";
	echo "sig: " . basename($msg['signature']) . "\n";
	//echo "raw: " . strlen($msg['raw']) . "\n";

	//print_r($msg);

	if ('yes' == $msg['verified']) {

		echo "\n\nParsing file:\n";
		$updates = new P2P_Updates($peer->UID);
		$updates->explode($msg['message'], $msg['priority'], $peer->UID);

	} else {
		$page->doXmlError('could not verify signature.');
	}

	echo "<ok/>";

?>
