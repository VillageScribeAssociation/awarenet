<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/inc/sync.inc.php');

//--------------------------------------------------------------------------------------------------
//	perform a complete sync with another peer (check all tables and files)
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->doXmlError('only admins may do this'); }

	//----------------------------------------------------------------------------------------------
	//	load the server's record
	//----------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->doXmlError('peer not supplied'); }
	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->doXmlError('peer not found'); }

	$report = sync_entireDatabase($peer->UID);
	echo $report;

?>
