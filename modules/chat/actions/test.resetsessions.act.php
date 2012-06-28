<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/peers.set.php');

//--------------------------------------------------------------------------------------------------
//*	reset the active session list for a specified peer
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and peer UID
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	
	$set = new Chat_Peers(true);

	if ('' == $req->ref) {
		foreach($set->members as $item) {
			echo $item['peerUID'] . " - " . $item['peerName'] . "(" . $item['peerUrl'] . ")" . "\n";
		}
		die();
	}

	$peer = new Chat_Peer($req->ref, true);
	if (false == $peer->loaded) { $page->do404('Unknown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	query central server
	//----------------------------------------------------------------------------------------------
	
	$io = new Chat_IO();
	$result = $io->send('getsessions', $req->ref, '');
	echo "<b>Response:</b>";
	echo "<textarea rows='10' style='width: 100%'>$result</textarea><br/>\n";

?>
