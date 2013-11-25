<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//*	go through the database and look for gifts for the given peer
//--------------------------------------------------------------------------------------------------
//ref: UID of a P2P_Peer object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == trim($kapenta->request->ref)) { $page->do404('Peer not specified.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $page->do404('Unknown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	search for files
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Searching files</h1>\n";

	$set = new P2P_Offers($peer->UID);
	$count = $set->scanFiles(true);
	$msg = "Searched files for new gifts, found $count items.";
	echo $msg;

	//----------------------------------------------------------------------------------------------
	//	search database objects
	//----------------------------------------------------------------------------------------------
	echo "<h1>Searching database</h1>\n";
	$count = $set->scanDb(true);
	$msg = "Searched database for new gifts, found $count items.";
	echo $msg;

	//----------------------------------------------------------------------------------------------
	//	redirect pack to peers page
	//----------------------------------------------------------------------------------------------
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');
	//$page->do302('p2p/peers/');

?>
