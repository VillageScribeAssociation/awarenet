<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	request a peer resynchronize all tables, narrowcasted
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Peer UID not given'); }
	if ('' == $kapenta->registry->get('p2p.server.uid')) { $kapenta->page->do403('This peer is not configured.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->do404('Unknown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	broadcast resync request
	//----------------------------------------------------------------------------------------------

	$message = ''
	 . "\t<resynchronize>\n"
	 . "\t\t<peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
	 . "\t</resynchronize>\n";

	$detail = array(
		'message' => $message,
		'peer' => $peer->UID,
		'priority' => '1'
	);

	$kapenta->raiseEvent('*', 'p2p_narrowcast', $detail);

	//----------------------------------------------------------------------------------------------
	//	redirect back to peers list
	//----------------------------------------------------------------------------------------------
	$kapenta->session->msgAdmin('Requested peer resyncronize all content.', 'ok');
	$kapenta->page->do302('p2p/peers/');

?>
