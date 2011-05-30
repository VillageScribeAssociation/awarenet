<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');
	require_once($kapenta->installPath . 'modules/users/inc/commonpass.inc.php');

//--------------------------------------------------------------------------------------------------
//*	for changing password
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action nto specified.'); }
	if ('changeUserPass' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	// users may only change their own password
	if (('admin' != $user->role) AND ($user->UID != $_POST['UID'])) { $page->do403(); }

	// load user record (it's already in $user, load it anyway)
	$model = new Users_User($_POST['UID']);
	if (false == $model->loaded) { $page->do404('User not found.'); }

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

	// admins do not need the current password to change it
	if (('admin' == $user->role) && ('admin' != $model->role)) {
	//if ('admin' == $user->role) {  
		$msg = '';
		$allOk = true;
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
			$commonPass = users_commonPasswords();
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

	if (true == $allOk) {
		$model->password = sha1($pwdNew . $model->UID);
		$model->save();
		$session->msg('Your password has been changed.', 'ok');
		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/' . $model->alias);

	} else {
		$session->msg('Your password was not changed:<br/>' . $msg, 'bad');
		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/' . $model->alias);
	}

?>
