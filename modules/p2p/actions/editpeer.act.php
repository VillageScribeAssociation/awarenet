<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit details of a peer server
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check referenace and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if ('' == trim($kapenta->request->ref)) { $kapenta->page->do404(); }

	$model = new P2P_Peer($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Unkown peer.'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/p2p/actions/editpeer.page.php');
	$kapenta->page->blockArgs['UID'] = $kapenta->request->ref;
	$kapenta->page->render();

?>
