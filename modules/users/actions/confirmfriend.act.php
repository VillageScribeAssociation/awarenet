<?

//--------------------------------------------------------------------------------------------------
//	confirm a friend request, or ignore one
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public users cannot have friends
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] == 'public') { do403(); }

	//----------------------------------------------------------------------------------------------
	//	OK, confirm the rquest
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true) 
		AND ($_POST['action'] == 'confirmFriendReq') 
		AND (array_key_exists('friendUID', $_POST) == true) 
		AND (dbRecordExists('users', $_POST['friendUID']) == true) ) {

		$retLink = 'users/friends/';
		$relationship = 'friend';
		if (true == array_key_exists('relationship', $_POST))
			{ $relationship = clean_string($_POST['relationship']); }

		$model = new Friendship();
	
		// if friendship already exists
		if (true == $model->linkExists($user->data['UID'], $_POST['friendUID'])) {do302($retLink);}

		// if friend request has been withdrawn
		if (false == $model->linkExists($_POST['friendUID'], $user->data['UID'])) {do302($retLink);}

		//------------------------------------------------------------------------------------------
		//	add return link
		//------------------------------------------------------------------------------------------

		$friendUID = clean_string($_POST['friendUID']);

		$model->data['UID'] = createUID();
		$model->data['userUID'] = $user->data['UID'];
		$model->data['friendUID'] = $friendUID;
		$model->data['relationship'] = $relationship;
		$model->data['status'] = 'confirmed';
		$model->data['createdOn'] = mysql_datetime();
		$model->save();

		//------------------------------------------------------------------------------------------
		//	update original request
		//------------------------------------------------------------------------------------------

		$sql = "update friendships set status='confirmed' "
			 . "where userUID='" . sqlMarkup($friendUID) . "'"
			 . " and friendUID='" . $user->data['UID'] . "'";

		dbQuery($sql);

		//------------------------------------------------------------------------------------------
		//	send notification
		//------------------------------------------------------------------------------------------

		$title = $user->getName() . " confirmed your friend request.";

		$content = "Your relationship on their profile is: " . $relationship . ".";

		$url = '/users/friends/';
		$fromUrl = '/users/profile/' . $user->data['UID'];
		$imgRow = imgGetHeaviest('users', $user->data['UID']);
		$imgUID = '';
		if (false != $imgRow) { $imgUID = $imgRow['UID']; }

		notifyUser(	$friendUID, createUID(), $user->getName(), 
					$fromUrl, $title, $content, $url, $imgUID );

		//------------------------------------------------------------------------------------------
		//	go back to friends pages
		//------------------------------------------------------------------------------------------

		$_SESSION['sMessage'] .= "You have confirmed a friend request.<br/>\n";
		do302($retLink);

	}

	do404();

?>
