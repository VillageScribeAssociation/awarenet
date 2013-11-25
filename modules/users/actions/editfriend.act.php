<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	page for changing relationship properties
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	load the friendship and check identity/permissions
	//---------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); }
	if (false == $db->objectExists('users_user', $kapenta->request->ref)) { $page->do404(); }

	$friendUID = $kapenta->request->ref;
	$model = new Users_Friendship();
	$model->loadFriend($user->UID, $friendUID);
	if (false == $model->loaded) { $page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	render page
	//---------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/editfriend.page.php');
	$kapenta->page->blockArgs['userUID'] = $user->UID;
	$kapenta->page->blockArgs['friendUID'] = $friendUID;
	$kapenta->page->blockArgs['userRa'] = $user->alias;
	$kapenta->page->blockArgs['userName'] = $user->getName();
	$kapenta->page->blockArgs['friendshipUID'] = $model->UID;
	$kapenta->page->render();
	

?>
