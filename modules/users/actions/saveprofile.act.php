<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	update a user profile
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveProfile' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('User not specified (UID).'); }

	$model = new Users_User($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Unkonw user.'); }

	$authorised = false;
	if ($user->UID == $_POST['UID']) { $authorised = true; }
	if ('admin' == $user->role) { $authorised = true; }
	if (false == $authorised) { $kapenta->page->do403('Upi cannot edit this profile'); }
	//TODO: more rigorous, standard permissions

	//----------------------------------------------------------------------------------------------
	//	if user has permissions
	//----------------------------------------------------------------------------------------------
	$diff = '';

	foreach($model->profile as $field => $value) {
		if (true == array_key_exists($field, $_POST)) {
			$newVal = htmlentities($_POST[$field]);
			if (($model->profile[$field] != $newVal) && ('' != trim($newVal))) {
				$confidential = false;

				if ('tel' == $field) { $confidential = true; }
				if ('email' == $field) { $confidential = true; }

				if (false == $confidential) {
					$diff .= "<b>$field:</b> " . $newVal . "<br/>\n";
				}
			}

			//--------------------------------------------------------------------------------------
			// birthyear is a special case, check it's a 4 digit number
			//--------------------------------------------------------------------------------------
			if ('birthyear' == $field) {
				$_POST['birthyear'] = trim($_POST['birthyear']);
				if ( (strlen($newVal) != 4) || (false == is_numeric($newVal)) )
					{ $_POST['birthyear'] = ''; }
			}

			$model->profile[$field] = $newVal;
		}
	}

	$report = $model->save();
	if ('' == $report) { $session->msg('Profile updated.', 'ok'); }
	else { $session->msg('Could not update profile: ' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	send notification to users and their friends //TODO: handle with event
	//----------------------------------------------------------------------------------------------
	if ('' != trim($diff)) {
		$title = $model->getName() . "'s profile has changed.";
		if ($model->UID == $user->UID)
			{ $title = $user->getName() . " has updated their profile.";	}

		$url = '/users/profile/'  . $user->UID;
		$nUID = $notifications->create(
			'users', 'users_user', $model->UID, 'users_editprofile', $title, $diff, $url
		);
		$notifications->addUser($nUID, $model->UID);
		$notifications->addFriends($nUID, $model->UID);
	}

	//------------------------------------------------------------------------------------------
	//	redirect back to profile
	//------------------------------------------------------------------------------------------
	if (array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	$kapenta->page->do302('users/profile/' . $model->alias);

?>
