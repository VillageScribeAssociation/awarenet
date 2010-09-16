<?

//--------------------------------------------------------------------------------------------------
//*	confirm a friend request, or ignore one
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check post vars and permissions
	//----------------------------------------------------------------------------------------------
	if ($user->role == 'public') { $page->do403(); }			// public users cannot have friends

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('confirmFriendReq' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('friendUID', $_POST)) { $page->do404('No friendUID sent.'); }
	if (false == $db->objectExists('Users_User', $_POST['friendUID']))
		{ $page->do404('Friend UID not found.'); }

	//----------------------------------------------------------------------------------------------
	//	OK, confirm the request
	//----------------------------------------------------------------------------------------------
	$retLink = 'users/friends/';
	$relationship = 'friend';
	if (true == array_key_exists('relationship', $_POST))
			{ $relationship = $utils->cleanString($_POST['relationship']); }

	$model = new Users_Friendship();
	
	// if friendship already exists
	if (true == $model->linkExists($user->UID, $_POST['friendUID'])) {$page->do302($retLink);}

	// if friend request has been withdrawn
	if (false == $model->linkExists($_POST['friendUID'], $user->UID)) {$page->do302($retLink);}

	//----------------------------------------------------------------------------------------------
	//	add return link
	//----------------------------------------------------------------------------------------------

	$friendUID = $utils->cleanString($_POST['friendUID']);

	$model->UID = $kapenta->createUID();
	$model->userUID = $user->UID;
	$model->friendUID = $friendUID;
	$model->relationship = $relationship;
	$model->status = 'confirmed';
	$model->createdOn = $db->datetime();
	$model->save();

	//----------------------------------------------------------------------------------------------
	//	update original request
	//----------------------------------------------------------------------------------------------

	$recip = new Users_Friendship();
	$loaded = $recip->loadFriend($model->friendUID, $model->userUID);
	if (true == $loaded) {
		$recip->status = 'confirmed';
		$recip->save();
	}

	//----------------------------------------------------------------------------------------------
	//	send notification to friend
	//----------------------------------------------------------------------------------------------
	/*
	$title = $user->getName() . " confirmed your friend request.";

	$content = "Your relationship on their profile is: " . $relationship . ".";

	$url = '/users/friends/';
	$fromUrl = '/users/profile/' . $user->UID;
	$imgRow = imgGetDefault('users', $user->UID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	notifyUser(	$friendUID, kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );
	*/

	//----------------------------------------------------------------------------------------------
	//	send notification to user who confirmed
	//----------------------------------------------------------------------------------------------
	/*
	$title = "You confirmed a friend request from [[:users::name::UID=". $friendUID .":]].";

	$content = "Your relationship on their profile is: " . $recip->relationship . ".";

	$url = '/users/friends/';
	$fromUrl = '/users/profile/' . $friendUID;
	$imgRow = imgGetDefault('users', $friendUID);
	$imgUID = '';
	if (false != $imgRow) { $imgUID = $imgRow['UID']; }

	notifyUser(	$user->UID, kapenta->createUID(), $user->getName(), 
				$fromUrl, $title, $content, $url, $imgUID );
	*/
	//----------------------------------------------------------------------------------------------
	//	go back to friends pages
	//----------------------------------------------------------------------------------------------

	$session->msg('You have confirmed a friend request.', 'ok');
	$page->do302($retLink);

?>
