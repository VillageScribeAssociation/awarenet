<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//*	development action to test downloads listing
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Peer UID not given.'); }

	$peer = new P2p_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->do404('Peer not found.'); }

	//----------------------------------------------------------------------------------------------
	//	get download list from peer
	//----------------------------------------------------------------------------------------------
	$xml = 	$peer->sendMessage('downloads', $kapenta->time());
	echo "<b>Download list:</b><br/>";
	echo "<textarea rows='10' style='width:100%;'>$xml</textarea>";

	$downloads = new P2P_Downloads();
	$meta = $downloads->expandXml($xml);

	foreach ($meta as $download) {
		echo ''
		 . "fileName: " . $download['fileName'] . "<br/>\n"
		 . "manifest: " . $download['manifest'] . "<br/>\n"
		 . "parts: " . $download['parts'] . "<br/>\n"
		 . "<br/>\n";
	}

?>
