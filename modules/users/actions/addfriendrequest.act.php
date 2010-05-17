<?

//--------------------------------------------------------------------------------------------------
//	make a friend request
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	public users can't add friends
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }
	require_once($installPath . 'modules/users/models/friendship.mod.php');

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

		$friendUID = clean_string($_POST['friendUID']);
		$friendName = expandBlocks("[[:users::name::userUID=" . $friendUID . ":]]", '');
		$fStatus = 'unconfirmed';

		$model = new Friendship();

		//------------------------------------------------------------------------------------------
		//	ignore duplicates (if we're already a friend or have already requested to be)
		//------------------------------------------------------------------------------------------
		if (true == $model->linkExists($user->data['UID'], $friendUID)) {do302($retLink);}

		//------------------------------------------------------------------------------------------
		//	confirm friendship if other party has already asked to be our friend
		//------------------------------------------------------------------------------------------
		if (true == $model->linkExists($friendUID, $user->data['UID'])) { 

			$recip = new Friendship();
			$recip->loadFriend($friendUID, $user->data['UID']);
			$recip->data['status'] = 'confirmed';
			$recip->save();

			$fStatus = 'confirmed'; 

			//-------------------------------------------------------------------------------------
			//	send notification to other party
			//-------------------------------------------------------------------------------------

			$title = $user->getName() . " confirmed your friend request.";
	
			$content = "Your relationship on their profile is: " . $relationship . ".";

			$url = '/users/friends/';
			$fromUrl = '/users/profile/' . $user->data['UID'];
			$imgRow = imgGetDefault('users', $user->data['UID']);
			$imgUID = '';
			if (false != $imgRow) { $imgUID = $imgRow['UID']; }

			notifyUser(	$friendUID, createUID(), $user->getName(), 
						$fromUrl, $title, $content, $url, $imgUID );

			//-------------------------------------------------------------------------------------
			//	send notification to own feed
			//-------------------------------------------------------------------------------------

			$title = "You have confirmed a friend request from " . $friendName . ".";
	
			$content = "Your relationship on their profile is: " 
					 . $recip->data['relationship'] . ".";

			$url = '/users/profile/' . $friendUID;
			$fromUrl = '/users/profile/';
			$imgRow = imgGetDefault('users', $friendUID);
			$imgUID = '';
			if (false != $imgRow) { $imgUID = $imgRow['UID']; }

			notifyUser(	$user->data['UID'], createUID(), $user->getName(), 
						$fromUrl, $title, $content, $url, $imgUID );


		}

		//------------------------------------------------------------------------------------------
		//	save record
		//------------------------------------------------------------------------------------------

		$model->data['UID'] = createUID();
		$model->data['userUID'] = $user->data['UID'];
		$model->data['friendUID'] = $friendUID;
		$model->data['relationship'] = $relationship;
		$model->data['status'] = $fStatus;
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

		if ($fStatus == 'unconfirmed') {
			$_SESSION['sMessage'] .= "You have made a friend request.<br/>\n";
		} else {	
			$_SESSION['sMessage'] .= "You have confirmed a friend request from $friendName.<br/>\n";
		}

		do302($retLink);

	}

	do404();

?>
