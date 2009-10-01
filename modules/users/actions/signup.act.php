<?

//--------------------------------------------------------------------------------------------------
//	page for signing up users
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	form variables
	//----------------------------------------------------------------------------------------------

	$formvars = array(
		'UID' => createUID(),
		'school' => '261390791197222710',
		'grade' => '12',
		'firstname' => '',	
		'surname' => '',
		'username' => '',	
		'password' => '',
		'lang' => 'en',	
		'pass1' => '',
		'pass2' => ''
	);

	//----------------------------------------------------------------------------------------------
	//	get form values from POST
	//----------------------------------------------------------------------------------------------

	foreach($formvars as $field => $val) { 
		if (array_key_exists($field, $_POST) == true) { $formvars[$field] = trim($_POST[$field]); }
	}

	//----------------------------------------------------------------------------------------------
	//	tests
	//----------------------------------------------------------------------------------------------
	
	$report = '';
	$showPage = true;

	if ((array_key_exists('action', $_POST)) AND ($_POST['action'] == 'userSignup')) {

		// basic tests
		if ($formvars['pass1'] != $formvars['pass2']) 
			{ $report .= "[*] Passwords do not match.<br/>\n"; }

		if (strlen(trim($formvars['pass1'])) < 4)
			{ $report .= "[*] Please choose a password of more than four characters.<br/>\n"; }

		if (strlen(trim($formvars['username'])) < 4) 
			{ $report .= "[*] Please choose a username of four or more characters.<br/>\n"; }

		if (strlen(trim($formvars['surname'])) < 1) 
			{ $report .= "[*] Please add your surname.<br/>\n"; }	

		if (strlen(trim($formvars['firstname'])) < 1) 
			{ $report .= "[*] Please add your first name.<br/>\n"; }	

		// check if user is already registered
		$sql = "select * from users where lower(username)='". sqlMarkup($formvars['username']) ."'";
		$result = dbQuery($sql);
		if (dbNumRows($result) != 0) { 
			$report .= "[*] The username <i>" . $formvars['username']. "</i> has already been "
					 . "registered, please choose another.<br/>\n"; 
		}	

		// check that school exists
		if (dbRecordExists('schools', $formvars['school']) == false) 
			{ $report .= "[*] Please choose a school from the list.<br/>\n"; }	

		if ($report == '') {
			//--------------------------------------------------------------------------------------
			//	create the account
			//--------------------------------------------------------------------------------------
			$_SESSION['sMessage'] .= "Creating your account...<br/>\n";

			$model = new Users();
			$model->data['UID'] = createUID();
			$model->data['school'] = clean_string($formvars['school']);
			$model->data['ofGroup'] = 'student';
			$model->data['grade'] = clean_string($formvars['grade']);
			$model->data['firstname'] = clean_string($formvars['firstname']);
			$model->data['surname'] = clean_string($formvars['surname']);
			$model->data['username'] = clean_string($formvars['username']);
			$model->data['password'] = sha1($formvars['pass1'] . $model->data['UID']);
			$model->data['lang'] = 'en';
			$model->data['profile'] = '';
			$model->data['permissions'] = '';
			$model->data['lastOnline'] = mysql_datetime();
			$model->data['createdOn'] = mysql_datetime();
			$model->data['createdBy'] = 'admin';
			$model->save();

			authUpdatePermissions();

			//--------------------------------------------------------------------------------------
			//	sign user in
			//--------------------------------------------------------------------------------------
			$_SESSION['sUser'] = $model->data['username'];
			$_SESSION['sUserUID'] = $model->data['UID'];
			$_SESSION['sMessage'] .= "You are now logged in.<br/>\n";
			$user->load($model->data['UID']);
			do302('users/profile/'); // show user his profile

		} else {
			//--------------------------------------------------------------------------------------
			//	not enough info yet
			//--------------------------------------------------------------------------------------
			$report = "<b>Before you continue:</b><br/>\n" . $report . "<br/><br/>\n";
			$_SESSION['sMessage'] .= $report;
		}

	}

	//----------------------------------------------------------------------------------------------
	//	show page
	//----------------------------------------------------------------------------------------------

	if ($showPage == true) {
		$page->load($installPath . 'modules/users/actions/signup.page.php');
		foreach($formvars as $field => $value) { $page->blockArgs[$field] = $value; }
		$page->render();
	}

?>
