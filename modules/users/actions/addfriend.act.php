<?

//--------------------------------------------------------------------------------------------------
//	add a user as a friend (DEPRECTED, REDUNDANT, REMOVE?)
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/users/models/friendship.mod.php');

	if ($request['ref'] == '') { do404(); }
	raFindRedirect('users', 'addfriend', 'users', $request['ref']);

	//----------------------------------------------------------------------------------------------
	//	check that the friendship does not already exist
	//----------------------------------------------------------------------------------------------

	$friend = new User($request['ref']);
	if ($user->isFriend($friend->data['UID'])) {
		$_SESSION['sMessage'] .= "You are already friends.<br/>\n";
		do302('users/profile/' . $request['ref']);
	}

	//----------------------------------------------------------------------------------------------
	//	create friend request
	//----------------------------------------------------------------------------------------------
	
	$model = new Friendship();
	$model->data['UID'] = createUID();
	$model->data['friendUID'] = $friend->data['UID'];
	$model->data['relationship'] = 'friend';
	$model->data['status'] = 'unconfirmed';
	$model->save();

	$_SESSION['sMessage'] .= "You have made a friend request.<br/>";
	do302('users/profile/' . $request['ref']);

?>
