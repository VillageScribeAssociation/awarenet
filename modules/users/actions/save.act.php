<?

//-------------------------------------------------------------------------------------------------
//*	save changes to a Users_User object
//-------------------------------------------------------------------------------------------------
//	Permissions: a user may save changes to their own forename, surname, password, profile and 
//	language.  Admins may change most other fields.

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveUserRecord' != $_POST['action']) { $page->do404('Action not supported.'); }

	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed.'); }

	$model = new Users_User($_POST['UID']);	
	if (false == $model->loaded) { $page->do404('User not found.'); }
	$oldRole = $model->role;

	//----------------------------------------------------------------------------------------------
	//	if admin editing any record
	//----------------------------------------------------------------------------------------------
	if ('admin' == $user->role) {
		$report = '';

		foreach($_POST as $field => $value) {
			switch(strtolower($field)) {
				case 'ofGroup':		$model->role = $utils->cleanString($value); 		break;
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

		$newRole = $model->role;
		$hasTel = false;
		if ((true == array_key_exists('tel', $model->profile)) && ('' != $model->profile['tel'])) {
			$hasTel = true;
		}

		if ((false == $hasTel) && ('teacher' == $newRole) && ('teacher' != $oldRole)) {
			$report .= "This user cannot be made a teacher without a telephone number.<br/>";
		}

		if ('' == $report) { $report = $model->save(); }

		if ('' == $report) { $session->msg('User account updated.', 'ok'); }
		else { $session->msg($report, 'bad'); }

		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/list/');
	}

	//----------------------------------------------------------------------------------------------
	//	if non-admin editing own record
	//----------------------------------------------------------------------------------------------
	if (($user->UID == $_POST['UID']) AND ('admin' != $user->role)) {
		$model = new Users_User($_POST['UID']);
		if (false == $model->loaded) { $page->do404("Could not load User " . $model->UID);}
		foreach($_POST as $field => $value) {
			switch(strtolower($field)) {
				case 'firstname':	$model->firstname = $utils->cleanString($value); 	break;
				case 'surname':		$model->surname = $utils->cleanString($value); 		break;
				case 'lang':		$model->lang = $utils->cleanString($value); 		break;
			}
		}

		$report = $model->save();
		if ('' == $report) { $session->msg('Your account has been updated.', 'ok'); }
		else { $session->msg($report, 'bad'); }
		
		if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
		$page->do302('users/profile/' . $model->alias);		

	}

?>
