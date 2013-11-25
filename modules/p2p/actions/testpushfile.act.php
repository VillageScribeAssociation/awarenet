<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	test push cycle for files
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404('Peer not specified.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $page->do404('Unknown peer.'); }

	$client = new P2P_Client($peer->UID);

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	$report = $client->pushFiles();
	echo $report;

?>
