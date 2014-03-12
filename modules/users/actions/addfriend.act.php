<?

//--------------------------------------------------------------------------------------------------
//*	add a user as a friend (DEPRECTED, REDUNDANT, REMOVE?)
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('users', 'addfriend', 'users', $kapenta->request->ref);

	//----------------------------------------------------------------------------------------------
	//	check that the friendship does not already exist
	//----------------------------------------------------------------------------------------------
	$friend = new Users_User($UID);
	if ($kapenta->user->isFriend($friend->UID)) {
		$kapenta->session->msg("You are already friends.", 'bad');
		$kapenta->page->do302('users/profile/' . $kapenta->request->ref);
	}

	//----------------------------------------------------------------------------------------------
	//	create friend request
	//----------------------------------------------------------------------------------------------
	$model = new Users_Friendship();
	$model->userUID = $kapenta->user->UID;
	$model->friendUID = $friend->UID;
	$model->relationship = 'friend';
	$model->status = 'unconfirmed';
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect back to user's profile
	//----------------------------------------------------------------------------------------------
	$kapenta->session->msg("You have made a friend request.<br/>", 'ok');
	$kapenta->page->do302('users/profile/' . $kapenta->request->ref);

?>
