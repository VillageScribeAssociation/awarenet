<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	confirm deletion of a P2P_Peer object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------		
	if (false == array_key_exists('UID', $kapenta->request->args)) { $kapenta->page->do404(); }

	$model = new P2P_Peer($kapenta->request->args['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Peer not found.'); }
	if (false == $user->authHas('p2p', 'p2p_peer', 'delete', $model->UID))
		{ $kapenta->page->do403('You are not authorized to delete this peer.'); }	
	
	//----------------------------------------------------------------------------------------------
	//	make the confirmation block
	//----------------------------------------------------------------------------------------------		
	$labels = array('UID' => $model->UID, 'raUID' => $model->UID);
	$block = $theme->loadBlock('modules/p2p/views/confirmdelete.block.php');
	$html = $theme->replaceLabels($labels, $block);
	$session->msg($html, 'warn');
	$kapenta->page->do302('p2p/editpeer/' . $model->UID);

?>
