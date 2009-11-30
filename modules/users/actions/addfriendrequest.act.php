<?

//--------------------------------------------------------------------------------------------------
//	make a friend request
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public users can't add friends
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] == 'public') { do403(); }
	require_once($installPath . 'modules/users/models/friendships.mod.php');

	//----------------------------------------------------------------------------------------------
	//	OK, make the rquest
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true) 
		AND ($_POST['action'] == 'addFriendReq') 
		AND (array_key_exists('friendUID', $_POST) == true) 
		AND (dbRecordExists('users', $_POST['friendUID']) == true) ) {

		$retLink = 'users/profile/' . $_POST['friendUID'];
		if ( (array_key_exists('return', $_POST) == true) 
		   AND ($_POST['return'] == 'search') ) { $retLink = 'users/find/'; }

		$relationship = 'friend';
		if (true == array_key_exists('relationship', $_POST))
			{ $relationship = clean_string($_POST['relationship']); }

		$model = new Friendship();
		if (true == $model->linkExists($user->data['UID'], $_POST['friendUID'])) {do302($retLink);}

		$friendUID = clean_string($_POST['friendUID']);

		//------------------------------------------------------------------------------------------
		//	save record
		//------------------------------------------------------------------------------------------

		$model->data['UID'] = createUID();
		$model->data['userUID'] = $user->data['UID'];
		$model->data['friendUID'] = $friendUID;
		$model->data['relationship'] = $relationship;
		$model->data['status'] = 'unconfirmed';
		$model->data['createdOn'] = mysql_datetime();
		$model->save();

		//------------------------------------------------------------------------------------------
		//	send notification
		//------------------------------------------------------------------------------------------

		$title = $user->getName() . " sent you a friend request.";

		$content = "If you accept this request, your names will appear on each others profiles.";

		$url = '/users/friends/';
		$fromUrl = '/users/profile/' . $user->data['UID'];
		$imgRow = imgGetDefault('users', $user->data['UID']);
		$imgUID = '';
		if (false != $imgRow) { $imgUID = $imgRow['UID']; }

		notifyUser(	$friendUID, createUID(), $user->getName(), 
					$fromUrl, $title, $content, $url, $imgUID );

		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------

		$_SESSION['sMessage'] .= "You have made a friend request.<br/>\n";
		do302($retLink);

	}

	do404();

?>
