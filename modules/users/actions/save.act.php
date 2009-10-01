<?

//-------------------------------------------------------------------------------------------------
//	save changes to a user record
//-------------------------------------------------------------------------------------------------
//	Permissions: a user may save changes to their own forename, surname, password, profile and 
//	language.  Admins may change most other fields.

//-------------------------------------------------------------------------------------------------
//	for changing fields othert than password
//-------------------------------------------------------------------------------------------------

if ((array_key_exists('action', $_POST) == true)
	AND ($_POST['action'] == 'saveUserRecord')
	AND (array_key_exists('UID', $_POST) == true)
 	AND (dbRecordExists('users', $_POST['UID']) == true)
	) {

	//----------------------------------------------------------------------------------------------
	//	if admin editing any record
	//----------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] == 'admin') {
		$u = new Users($_POST['UID']);
		if (array_key_exists('ofGroup', $_POST)) 	{ $u->data['ofGroup'] = $_POST['ofGroup']; }
		if (array_key_exists('school', $_POST))		{ $u->data['school'] = $_POST['school']; }
		if (array_key_exists('grade', $_POST)) 		{ $u->data['grade'] = $_POST['grade']; }
		if (array_key_exists('firstname', $_POST)) 	{ $u->data['firstname'] = $_POST['firstname']; }
		if (array_key_exists('surname', $_POST)) 	{ $u->data['surname'] = $_POST['surname']; }
		if (array_key_exists('username', $_POST)) 	{ $u->data['username'] = $_POST['username']; }
		if (array_key_exists('lang', $_POST))	 	{ $u->data['lang'] = $_POST['lang']; }
		$u->save();
		
		if (array_key_exists('return', $_POST)) { do302($_POST['return']); }
		authUpdatePermissions();
		do302('/users/list/');
	}

	//----------------------------------------------------------------------------------------------
	//	if non-admin editing own record
	//----------------------------------------------------------------------------------------------

	if (($user->data['UID'] == $_POST['UID']) AND ($user->data['ofGroup'] != 'admin')) {
		$u = new Users($_POST['UID']);
		if (array_key_exists('firstname', $_POST)) 	{ $u->data['firstname'] = $_POST['firstname']; }
		if (array_key_exists('surname', $_POST)) 	{ $u->data['surname'] = $_POST['surname']; }
		if (array_key_exists('lang', $_POST))	 	{ $u->data['lang'] = $_POST['lang']; }
		$u->save();
		
		if (array_key_exists('return', $_POST)) { do302($_POST['return']); }
		authUpdatePermissions();
		do302('users/profile/' . $u->data['recordAlias']);
	}

}

//-------------------------------------------------------------------------------------------------
//	for changing password
//-------------------------------------------------------------------------------------------------

if ((array_key_exists('action', $_POST) == true)
	AND ($_POST['action'] == 'changeUserPass')
	AND (array_key_exists('UID', $_POST) == true)
 	AND (dbRecordExists('users', $_POST['UID']) == true)
	) {

	// users may only change their own password
	if (($user->data['ofGroup'] != 'admin') AND ($user->data['UID'] != $_POST['UID'])) { do403(); }

	// load user record (it's already in $user, load it anyway)
	$u = new Users($_POST['UID']);

	$pwdCurrent = trim($_POST['pwdCurrent']);
	$pwdNew = trim($_POST['pwdNew']);
	$pwdConfirm = trim($_POST['pwdConfirm']);

	//----------------------------------------------------------------------------------------------
	//	verify
	//----------------------------------------------------------------------------------------------

	$allOk = true;
	$msg = '';

	// check current password
	if ($u->data['password'] != sha1($pwdCurrent . $u->data['UID'])) {
		$msg .= "[*] Current password incorrent.<br/>\n";
		$allOk = false;
	}

	// check both submissions match
	if ($pwdNew != $pwdConfirm) {
		$msg .= "[*] Passwords do not match.<br/>\n";
		$allOk = false;
	} else {

		// check length
		if (strlen($pwdNew) < 6) {
			$msg .= "[*] Please choose a password of 6 or more characters.<br/>\n";
			$allOk = false;
		} else {

			// check for common passwords			
			$commonPass = '';		
			include $installPath . 'modules/users/inc/commonpass.inc.php';
			foreach($commonPass as $tooEasy) {
				if ($tooEasy == strtolower($pwdNew)) {
					$msg .= "[*] Your password is on our list of common passwords.<br/>\n";
					$msg .= "[>] Your password is too easy to guess, crack or brute force.<br/>\n";
					$msg .= "[>] If you use this password elsewhere you should change it.<br/>\n";
					$allOk = false;

				}
			}

		}

	}

	//----------------------------------------------------------------------------------------------
	//	save
	//----------------------------------------------------------------------------------------------

	if ($allOk == true) {
		$u->data['password'] = sha1($pwdNew . $u->data['UID']);
		$u->save();
		$_SESSION['sMessage'] .= "Your password has been changed.<br/>\n";
		if (array_key_exists('return', $_POST)) { do302($_POST['return']); }
		do302('users/profile/' . $_POST['UID']);

	} else {
		$_SESSION['sMessage'] .= "Your password was not changed:<br/><br/>\n" . $msg;
		if (array_key_exists('return', $_POST)) { do302($_POST['return']); }
		do302('users/profile/'. $_POST['UID']);

	}

}

//-------------------------------------------------------------------------------------------------
//	for updating a profile
//-------------------------------------------------------------------------------------------------

if ((array_key_exists('action', $_POST) == true)
	AND ($_POST['action'] == 'saveProfile')
	AND (array_key_exists('UID', $_POST) == true)
 	AND (dbRecordExists('users', $_POST['UID']) == true)
	) {

	$authorised = false;
	if ($user->data['UID'] == $_POST['UID']) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }

	//----------------------------------------------------------------------------------------------
	//	if user has permissions
	//----------------------------------------------------------------------------------------------

	if ($authorised == true) {

		$diff = '';

		$u = new Users($_POST['UID']);
		foreach($u->profile as $field => $value) {
			if (array_key_exists($field, $_POST) == true) {
				if ($u->profile[$field] != $_POST[$field]) {
					$diff .= "<b>$field:</b>" . $_POST[$field] . "<br/>\n";
				}
				$u->profile[$field] = $_POST[$field];
			}
		}
		$u->save();

		//------------------------------------------------------------------------------------------
		//	send notification to friends
		//------------------------------------------------------------------------------------------
		$noticeUID = createUID();
		$title = $user->getName() . " has updated their profile.";

		$url = '/users/profile/'  . $user->data['UID'];
		$fromUrl = '/users/profile/' . $user->data['UID'];
		$imgRow = imgGetHeaviest('users', $user->data['UID']);
		$imgUID = '';
		if (false != $imgRow) { $imgUID = $imgRow['UID']; }

		notifyFriends($user->data['UID'], $noticeUID, $user->getName(), 
								$fromurl, $title, $diff, $url, $imgUID );

		//------------------------------------------------------------------------------------------
		//	redirect back to profile
		//------------------------------------------------------------------------------------------
		if (array_key_exists('return', $_POST)) { do302($_POST['return']); }
		do302('users/profile/' . $u->data['recordAlias']);

	} else { do403(); }

}

?>
