<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/inc/sync.inc.php');

//-------------------------------------------------------------------------------------------------
//*	download all outstanding files from a peer
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check reference and admin permissions
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Peer UID not given.'); }

	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->do404('No such peer'); }

	//---------------------------------------------------------------------------------------------
	//	get a list of all files from the images module and add to download queue
	//---------------------------------------------------------------------------------------------

	$report = sync_allFiles($peer->UID);
	echo $report;

?>
