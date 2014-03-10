<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//-------------------------------------------------------------------------------------------------
//*	api to allow peer to annouce self by HTTP POST (requires recovery password)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check POST arguments
	//---------------------------------------------------------------------------------------------

	$require = array('recover', 'uid', 'name', 'url', 'pubkey', 'firewalled');

	foreach($require as $key) {
		if (false == array_key_exists($key, $_POST)) { $kapenta->page->doXmlError("Missing $key"); }
	}

	if ($_POST['recover'] != $kapenta->registry->get('kapenta.recoverypassword')) {
		$kapenta->page->doXmlError('Invalid password.');
	}

	//---------------------------------------------------------------------------------------------
	//	add or update a peer
	//---------------------------------------------------------------------------------------------

	$peer = new P2P_Peer($_POST['uid']);
	$peer->UID = $_POST['uid'];
	$peer->name = $_POST['name'];
	$peer->url = $_POST['url'];
	$peer->pubkey = $_POST['pubkey'];
	$peer->firewalled = $_POST['firewalled'];
	$report = $peer->save();


	if ('' == $report) {

		//-----------------------------------------------------------------------------------------
		//	broadcast resync request to new peer
		//-----------------------------------------------------------------------------------------

		$message = ''
		 . "\t<resynchronize>\n"
		 . "\t\t<peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
		 . "\t</resynchronize>\n";

		$detail = array(
			'message' => $message,
			'peer' => $peer->UID,
			'priority' => '1'
		);

		echo "<ok/>";

	} else {
		$kapenta->page->doXmlError($report);
	}

?>
