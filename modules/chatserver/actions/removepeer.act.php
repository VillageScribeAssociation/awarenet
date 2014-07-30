<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a peer
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $kapenta->request->ref) { $page->do404('Peer not given.'); }

	$model = new Chatserver_Peer($kapenta->request->ref);

	if (false == $model->loaded) { $page->do404('Unknown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the peer
	//----------------------------------------------------------------------------------------------

	$check = $model->delete();
	if (true == $check) { $session->msg("Removed peer server: " . $model->name); }
	else { $session->msg("Could not remove peer server: " . $model->UID); }

	//----------------------------------------------------------------------------------------------
	//	redirect back to chat server console
	//----------------------------------------------------------------------------------------------	
	$page->do302('chatserver/console/');

?>
