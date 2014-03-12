<?

//--------------------------------------------------------------------------------------------------
//*	confirm a friend request, or ignore one
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check post vars and permissions
	//----------------------------------------------------------------------------------------------
	if ($kapenta->user->role == 'public') { $kapenta->page->do403(); }			// public users cannot have friends

	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('confirmFriendReq' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('friendUID', $_POST)) { $kapenta->page->do404('No friendUID sent.'); }
	if (false == $kapenta->db->objectExists('users_user', $_POST['friendUID']))
		{ $kapenta->page->do404('Friend UID not found.'); }

	//----------------------------------------------------------------------------------------------
	//	OK, confirm the request
	//----------------------------------------------------------------------------------------------
	$retLink = 'users/friends/';
	$relationship = 'friend';
	if (true == array_key_exists('relationship', $_POST))
			{ $relationship = $utils->cleanString($_POST['relationship']); }

	$model = new Users_Friendship();
	
	// if friendship already exists
	if (true == $model->linkExists($kapenta->user->UID, $_POST['friendUID'])) {$kapenta->page->do302($retLink);}

	// if friend request has been withdrawn
	if (false == $model->linkExists($_POST['friendUID'], $kapenta->user->UID)) {$kapenta->page->do302($retLink);}

	//----------------------------------------------------------------------------------------------
	//	add return link
	//----------------------------------------------------------------------------------------------

	$friendUID = $utils->cleanString($_POST['friendUID']);

	$model->UID = $kapenta->createUID();
	$model->userUID = $kapenta->user->UID;
	$model->friendUID = $friendUID;
	$model->relationship = $relationship;
	$model->status = 'confirmed';
	$model->createdOn = $kapenta->db->datetime();
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
	//	raise event for notifications, etc
	//----------------------------------------------------------------------------------------------

	$args = array(
		'userUID' => $recip->friendUID,
		'friendUID' => $recip->userUID,
		'relationship' => $recip->relationship
	);

	$kapenta->raiseEvent('*', 'friendship_created', $args);

	//----------------------------------------------------------------------------------------------
	//	go back to friends pages
	//----------------------------------------------------------------------------------------------

	$kapenta->session->msg('You have confirmed a friend request.', 'ok');
	$kapenta->page->do302($retLink);

?>
