<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	request a peer resynchronize all tables, narrowcasted
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404('Peer UID not given'); }
	if ('' == $kapenta->registry->get('p2p.server.uid')) { $page->do403('This peer is not configured.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $page->do404('Unknown peer.'); }

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
	$session->msgAdmin('Requested peer resyncronize all content.', 'ok');
	$page->do302('p2p/peers/');

?>
