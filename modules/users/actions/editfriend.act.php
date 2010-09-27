<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	page for changing relationship properties
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	load the friendship and check identity/permissions
	//---------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	if (false == $db->objectExists('Users_User', $req->ref)) { $page->do404(); }

	$friendUID = $req->ref;
	$model = new Users_Friendship();
	$model->loadFriend($user->UID, $friendUID);
	if (false == $model->loaded) { $page->do404(); }

	//---------------------------------------------------------------------------------------------
	//	render page
	//---------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/editfriend.page.php');
	$page->blockArgs['userUID'] = $user->UID;
	$page->blockArgs['friendUID'] = $friendUID;
	$page->blockArgs['userRa'] = $user->alias;
	$page->blockArgs['userName'] = $user->getName();
	$page->blockArgs['friendshipUID'] = $model->UID;
	$page->render();
	

?>
