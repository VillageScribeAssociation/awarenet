<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//-------------------------------------------------------------------------------------------------
//*	remove a user from your friend list
//-------------------------------------------------------------------------------------------------
//	users may only remove their own friends and requests made by or to them

	//---------------------------------------------------------------------------------------------
	//	remove previously accepted friend from friend list
	//---------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('removeFriend' == $_POST['action'])) {
		if (false == array_key_exists('friendshipUID', $_POST)) { $kapenta->page->do404(); }
		if (false == $kapenta->db->objectExists('users_friendship', $_POST['friendshipUID']))
		{ $kapenta->page->do404(); }

		$model = new Users_Friendship($_POST['friendshipUID']);
		if ($user->UID != $model->userUID) { $kapenta->page->do403(); }	// not mine to delete

		//-----------------------------------------------------------------------------------------
		//	delete this record, then load and delete reciprocal record
		//-----------------------------------------------------------------------------------------
		$model->delete();
		$model->loadFriend($model->friendUID, $user->UID);
		$model->delete();

		$msg = "[[:users::namelink::userUID=" . $model->userUID . ":]]"
			 . " removed from friends list.<br/>\n";

		$session->msg($msg, 'ok');
	}

	//---------------------------------------------------------------------------------------------
	//	ignore a friend request
	//---------------------------------------------------------------------------------------------
	//	expects the friend user's UID, not friendshipUID

	if ((true == array_key_exists('action', $_POST)) && ('ignoreRequest' == $_POST['action'])) {
		$model = new Users_Friendship();

		if (false == array_key_exists('friendUID', $_POST)) { $kapenta->page->do404(); }
		if (false == $kapenta->db->objectExists('users_user', $_POST['friendUID'])) { $kapenta->page->do404(); }

		$loaded = $model->loadFriend($_POST['friendUID'], $user->UID);
		if (false == $loaded) { $kapenta->page->doXmlError('friend request not found.'); }

		if ($user->UID != $model->friendUID) { $kapenta->page->do403(); } // not mine to delete

		$model->delete();

		$msg = "Ignored friend request from "
			 . "[[:users::namelink::userUID=" . $model->userUID . ":]]<br/>\n";

		$session->msg($msg, 'ok');

	}

	//---------------------------------------------------------------------------------------------
	//	withdraw a friend request
	//---------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('withdrawRequest' == $_POST['action'])) {
		$model = new Users_Friendship();
		if (false == array_key_exists('friendUID', $_POST)) { $kapenta->page->do404(); }
		if (false == $kapenta->db->objectExists('users_user', $_POST['friendUID'])) { $kapenta->page->do404(); }

		$loaded = $model->loadFriend($user->UID, $_POST['friendUID']);
		if (false == $loaded) { $kapenta->page->doXmlError('friend request not found.'); }

		if ($user->UID != $model->userUID) { $kapenta->page->do403(); } // not mine to delete

		$model->delete();

		$msg = "Withdrew friend request to "
			 . "[[:users::namelink::userUID=" . $model->friendUID . ":]]<br/>\n";

		$session->msg($msg);

	}

	//---------------------------------------------------------------------------------------------
	//	done, return to friend list
	//---------------------------------------------------------------------------------------------
	$kapenta->page->do302('users/friends/' . $user->alias);

?>
