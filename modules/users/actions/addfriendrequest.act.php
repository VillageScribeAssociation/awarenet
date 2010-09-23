<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');

//--------------------------------------------------------------------------------------------------
//*	make a friend request
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permission
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }			// public users can't add friends
	
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('addFriendReq' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('friendUID', $_POST)) { $page->do404('No friendUID given.'); }
	if (false == $db->objectExists('Users_User', $_POST['friendUID'])) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	OK, make the rquest
	//----------------------------------------------------------------------------------------------
	$retLink = 'users/profile/' . $_POST['friendUID'];

	if ((true == array_key_exists('return', $_POST)) AND ('search' == $_POST['return']))
		{ $retLink = 'users/find/'; }

	$relationship = 'friend';
	if (true == array_key_exists('relationship', $_POST))
		{ $relationship = $utils->cleanString($_POST['relationship']); }

	$friendUID = $utils->cleanString($_POST['friendUID']);
	$friendName = $theme->expandBlocks("[[:users::name::userUID=" . $friendUID . ":]]", '');
	$fStatus = 'unconfirmed';

	$model = new Users_Friendship();

	//----------------------------------------------------------------------------------------------
	//	ignore duplicates (if we're already a friend or have already requested to be)
	//----------------------------------------------------------------------------------------------
	if (true == $model->linkExists($user->UID, $friendUID)) {$page->do302($retLink);}

	//----------------------------------------------------------------------------------------------
	//	confirm friendship if other party has already asked to be our friend
	//----------------------------------------------------------------------------------------------
	if (true == $model->linkExists($friendUID, $user->UID)) { 

		$recip = new Users_Friendship();
		$recip->loadFriend($friendUID, $user->UID);
		$recip->status = 'confirmed';
		$recip->save();

		$fStatus = 'confirmed'; 

	}

	//----------------------------------------------------------------------------------------------
	//	send notification to other party
	//----------------------------------------------------------------------------------------------
	/*	TODO: re-add notifications
	$title = $user->getName() . " confirmed your friend request.";
	
	$content = "Your relationship on their profile is: " . $relationship . ".";

	$url = '/users/friends/';
	$fromUrl = '/users/profile/' . $user->UID;
	$imgRow = imgGetDefault('users', $user->UID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	notifyUser(	$friendUID, $kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );
	*/
	//----------------------------------------------------------------------------------------------
	//	send notification to own feed
	//----------------------------------------------------------------------------------------------
	/* 	TODO: re-add notifications
	$title = "You have confirmed a friend request from " . $friendName . ".";
	
	$content = "Your relationship on their profile is: " 
			 . $recip->relationship . ".";

	$url = '/users/profile/' . $friendUID;
	$fromUrl = '/users/profile/';
	$imgRow = imgGetDefault('users', $friendUID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	notifyUser(	$user->UID, $kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );

	}
	*/
	//----------------------------------------------------------------------------------------------
	//	save record
	//----------------------------------------------------------------------------------------------
	$model->UID = $kapenta->createUID();
	$model->userUID = $user->UID;
	$model->friendUID = $friendUID;
	$model->relationship = $relationship;
	$model->status = $fStatus;
	$model->createdOn = $db->datetime();
	$model->save();

	//------------------------------------------------------------------------------------------
	//	send notification
	//------------------------------------------------------------------------------------------
	/*	TODO: re-add notifications
	$title = $user->getName() . " sent you a friend request.";

	$content = "If you accept this request, your names will appear on each others profiles.";

	$url = '/users/friends/';
	$fromUrl = '/users/profile/' . $user->UID;
	$imgRow = imgGetDefault('users', $user->UID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	notifyUser(	$friendUID, $kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );
	*/
	//------------------------------------------------------------------------------------------
	//	redirect back
	//------------------------------------------------------------------------------------------

	if ('unconfirmed' == $fStatus) { $session->msg('You have made a friend request.', 'ok'); }
	else { $session->msg('You have confirmed a friend request from ' . $friendName, 'ok'); }

	$page->do302($retLink);

?>
