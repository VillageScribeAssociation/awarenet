<?

	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	action to test download of file from a peer
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Peer not specified.'); }

	$peer = new P2P_Peer($req->ref);
	if (false == $peer->loaded) { $page->do404('Unknown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	do it
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Testing file pull</h1>\n";

	$client = new P2P_Client($peer->UID);
	$report = $client->pullFiles();
	echo $report;

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
