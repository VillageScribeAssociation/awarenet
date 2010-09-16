<?

//-------------------------------------------------------------------------------------------------
//	save changes to a user record
//-------------------------------------------------------------------------------------------------
//	Permissions: a user may save changes to their own forename, surname, password, profile and 
//	language.  Admins may change most other fields.

//-------------------------------------------------------------------------------------------------
//	for changing fields other than password
//-------------------------------------------------------------------------------------------------

if ((true == array_key_exists('action', $_POST))
	AND ('saveUserRecord' == $_POST['action'])
	AND (true == array_key_exists('UID', $_POST))
 	AND (true == $db->objectExists('users', $_POST['UID']))
	) {

	//----------------------------------------------------------------------------------------------
	//	if admin editing any record
	//----------------------------------------------------------------------------------------------

	if ('admin' == $user->role) {
		$model = new Users_User($_POST['UID']);
		if (false == $model->loaded) { $page->do404("Could not load User $UID");}
		foreach($_POST as $field => $value) {
			switch(strtolower($field)) {
				case 'role':		$model->role = $utils->cleanString($value); 		break;
				case 'school':		$model->school = $utils->cleanString($value); 		break;
				case 'grade':		$model->grade = $utils->cleanString($value); 		break;
				case 'firstname':	$model->firstname = $utils->cleanString($value); 	break;
				case 'surname':		$model->surname = $utils->cleanString($value); 		break;
				case 'username':	$model->username = $utils->cleanString($value); 	break;
				case 'password':	$model->password = $utils->cleanString($value); 	break;
				case 'lang':		$model->lang = $utils->cleanString($value); 		break;
			}
		}
		$report = $model->save();

		if ('' == $report) { $session->msg('User account updated.', 'ok'); }
		else { $session->msg($report, 'bad'); }

		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('/users/list/');
	}

	//----------------------------------------------------------------------------------------------
	//	if non-admin editing own record
	//----------------------------------------------------------------------------------------------

	if (($user->UID == $_POST['UID']) AND ('admin' != $user->role)) {
		$model = new Users_User($_POST['UID']);
		if (false == $model->loaded) { $page->do404("Could not load User $UID");}
		foreach($_POST as $field => $value) {
			switch(strtolower($field)) {
				case 'firstname':	$model->firstname = $utils->cleanString($value); 	break;
				case 'surname':		$model->surname = $utils->cleanString($value); 		break;
				//case 'password':	$model->password = $utils->cleanString($value); 	break;
				case 'lang':		$model->lang = $utils->cleanString($value); 		break;
			}
		}

		$report = $model->save();
		if ('' == $report) { $session->msg('Your account has been updated.', 'ok'); }
		else { $session->msg($report, 'bad'); }
			
		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/' . $model->alias);		

	}

}

//-------------------------------------------------------------------------------------------------
//	for changing password	//TODO: make this a separate action, incoprorating strength tests
//-------------------------------------------------------------------------------------------------

if ((true == array_key_exists('action', $_POST))
	AND ('changeUserPass' == $_POST['action'])
	AND (true == array_key_exists('UID', $_POST))
 	AND (true == $db->objectExists('users', $_POST['UID']))
	) {

	// users may only change their own password
	if (('admin' != $user->role) AND ($user->UID != $_POST['UID'])) { $page->do403(); }

	// load user record (it's already in $user, load it anyway)
	$model = new Users_User($_POST['UID']);

	$pwdCurrent = trim($_POST['pwdCurrent']);
	$pwdNew = trim($_POST['pwdNew']);
	$pwdConfirm = trim($_POST['pwdConfirm']);

	//----------------------------------------------------------------------------------------------
	//	verify
	//----------------------------------------------------------------------------------------------

	$allOk = true;
	$msg = '';

	// check current password
	if ($model->password != sha1($pwdCurrent . $model->UID)) {
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
		$model->password = sha1($pwdNew . $model->UID);
		$model->save();
		$session->msg('Your password has been changed.', 'ok');
		if (array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/' . $_POST['UID']);

	} else {
		$session->msg('Your password was not changed:<br/>' . $msg, 'bad');
		if (array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/'. $_POST['UID']);

	}

}


?>
