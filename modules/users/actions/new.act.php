<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new User object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'Users_User', 'new'))
		{ $page->do403('You are not authorized to create new Users.'); }


	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Users_User();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'role':	$model->role = $utils->cleanString($value); break;
			case 'school':	$model->school = $utils->cleanString($value); break;
			case 'grade':	$model->grade = $utils->cleanString($value); break;
			case 'firstname':	$model->firstname = $utils->cleanString($value); break;
			case 'surname':	$model->surname = $utils->cleanString($value); break;
			case 'username':	$model->username = $utils->cleanString($value); break;
			case 'password':	$model->password = sha1($value . $model->UID); break;
			case 'lang':	$model->lang = $utils->cleanString($value); break;
			case 'profile':	$model->profile = $utils->cleanString($value); break;
			case 'permissions':	$model->permissions = $utils->cleanString($value); break;
			case 'lastonline':	$model->lastonline = $utils->cleanString($value); break;
			case 'alias':	$model->alias = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('New User: ' . $model->getNameLink() . '<br/>', 'ok');
		$page->do302('/users/' . $model->alias);
	} else {
		$session->msg('Could not create new User:<br/>' . $report);
	}

	/*

	$model = new Users_User();

	if (array_key_exists('school', $_POST)) { $model->school = $_POST['school']; }
	if (array_key_exists('grade', $_POST)) { $model->grade = $_POST['grade']; }
	if (array_key_exists('firstname', $_POST)) { $model->firstname = $_POST['firstname']; }
	if (array_key_exists('surname', $_POST)) { $model->surname = $_POST['surname']; }
	if (array_key_exists('username', $_POST)) { $model->username = $_POST['username']; }
	if (array_key_exists('role', $_POST)) { $model->role = $_POST['role']; }

	if (array_key_exists('password', $_POST)) { 
			$model->password = sha1($_POST['password'] . $model->UID); 
	}

	$verify = $model->verify();

	if ($verify == '') {
		$model->save();
		//authUpdatePermissions();
		$page->do302('users/list/');
	} else {
		$verify = str_replace("\n", "<br/>\n", $verify);
		$session->msg($verify, 'bad');
		$page->do302('users/list/');
	}
	
	*/
?>
