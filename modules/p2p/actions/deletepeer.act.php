<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a P2P_Peer object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('deleteRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST))
		{ $page->do404('Peer not specified (UID).'); }
    
	$model = new P2P_Peer($_POST['UID']);
	if (false == $user->authHas('p2p', 'p2p_peer', 'delete', $model->UID))
		{ $page->do403('You are not authorized to delete this peer.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the announcement and redirect
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$session->msg("Deleted peer: " . $model->name);
	$page->do302('p2p/peers/');

?>
