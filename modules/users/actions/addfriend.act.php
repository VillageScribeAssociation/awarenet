<?

//--------------------------------------------------------------------------------------------------
//*	add a user as a friend (DEPRECTED, REDUNDANT, REMOVE?)
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('users', 'addfriend', 'users', $req->ref);

	//----------------------------------------------------------------------------------------------
	//	check that the friendship does not already exist
	//----------------------------------------------------------------------------------------------
	$friend = new Users_User($UID);
	if ($user->isFriend($friend->UID)) {
		$session->msg("You are already friends.", 'bad');
		$page->do302('users/profile/' . $req->ref);
	}

	//----------------------------------------------------------------------------------------------
	//	create friend request
	//----------------------------------------------------------------------------------------------
	$model = new Users_Friendship();
	$model->userUID = $user->UID;
	$model->friendUID = $friend->UID;
	$model->relationship = 'friend';
	$model->status = 'unconfirmed';
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	redirect back to user's profile
	//----------------------------------------------------------------------------------------------
	$session->msg("You have made a friend request.<br/>", 'ok');
	$page->do302('users/profile/' . $req->ref);

?>
