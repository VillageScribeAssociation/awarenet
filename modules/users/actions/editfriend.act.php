<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	page for changing relationship properties
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	load the friendship and check identity/permissions
	//---------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	if (false == $kapenta->db->objectExists('users_user', $kapenta->request->ref)) { $kapenta->page->do404(); }

	$friendUID = $kapenta->request->ref;
	$model = new Users_Friendship();
	$model->loadFriend($kapenta->user->UID, $friendUID);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	render page
	//---------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/editfriend.page.php');
	$kapenta->page->blockArgs['userUID'] = $kapenta->user->UID;
	$kapenta->page->blockArgs['friendUID'] = $friendUID;
	$kapenta->page->blockArgs['userRa'] = $kapenta->user->alias;
	$kapenta->page->blockArgs['userName'] = $kapenta->user->getName();
	$kapenta->page->blockArgs['friendshipUID'] = $model->UID;
	$kapenta->page->render();
	

?>
