<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Users_User object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_user', 'new'))
		{ $kapenta->page->do403('You are not authorized to create new Users.'); }

	if (false == array_key_exists('username', $_POST)) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//*	check that username is not already registered
	//----------------------------------------------------------------------------------------------
	if ('' != $user->getUserUID(strtolower($_POST['username']))) {
		$session->msg('Could not create new User: Username already taken.<br/>');
		$kapenta->page->do302('users/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$report = '';
	$model = new Users_User();
	$model->UID = $kapenta->createUID();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'group':		$model->role = $utils->cleanString($value); break;
			case 'role':		$model->role = $utils->cleanString($value); break;
			case 'school':		$model->school = $utils->cleanString($value); break;
			case 'grade':		$model->grade = $utils->cleanString($value); break;
			case 'firstname':	$model->firstname = $utils->cleanString($value); break;
			case 'surname':		$model->surname = $utils->cleanString($value); break;
			case 'username':	$model->username = $utils->cleanString($value); break;
			case 'password':	$model->password = sha1($value . $model->UID); break;
			case 'lang':		$model->lang = $utils->cleanString($value); break;

			case 'tel':			$model->profile['tel'] = $utils->cleanString($value); break;
			case 'email':		$model->profile['email'] = $utils->cleanString($value); break;
			//case 'profile':		$model->profile = $utils->cleanString($value); break;
			//case 'permissions':	$model->permissions = $utils->cleanString($value); break;
			//case 'lastonline':	$model->lastonline = $utils->cleanString($value); break;
			//case 'alias':			$model->alias = $utils->cleanString($value); break;
		}
	}

	// check contact details, maybe add email check here
	if ('teacher' == $model->role) {
		if (false == array_key_exists('tel', $model->profile)) 
			{ $report .= "Teachers teachers can only be added with a contact telephone number."; }
		if ('' == trim($model->profile['tel'])) 
			{ $report .= "Teachers teachers can only be added with a contact telephone number."; }
	}

	if ('' == $report) { $report = $model->save(); }

    // reload session, fixed bug to do with new user obejct overriding current user session keys
    $session->load($user->UID);
    $session->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new user: ' . $model->getNameLink() . '<br/>', 'ok');
		$kapenta->page->do302('users/profile/' . $model->alias);
	} else {
		$session->msg('Could not create new User:<br/>' . $report);
		$kapenta->page->do302('users/' . $model->alias);
	}

?>
