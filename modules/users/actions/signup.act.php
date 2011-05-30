<?

//--------------------------------------------------------------------------------------------------
//*	page for signing up users
//--------------------------------------------------------------------------------------------------
//TODO: tidy this up, add settings and permissions appropriate to this

	//----------------------------------------------------------------------------------------------
	//	form variables
	//----------------------------------------------------------------------------------------------

	$formvars = array(
		'UID' => $kapenta->createUID(),
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
		if (true == array_key_exists($field, $_POST)) { $formvars[$field] = trim($_POST[$field]); }
	}

	//----------------------------------------------------------------------------------------------
	//	tests
	//----------------------------------------------------------------------------------------------
	
	$report = '';
	$showPage = true;

	if ((true == array_key_exists('action', $_POST)) AND ('userSignup' == $_POST['action'])) {

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
		$sql = "select * from users_user"
			 . " where lower(username)='" . $db->addMarkup($formvars['username']) ."'";

		$result = $db->query($sql);

		if ($db->numRows($result) != 0) { 
			$report .= "[*] The username <i>" . $formvars['username']. "</i> has already been "
					 . "registered, please choose another.<br/>\n"; 
		}	

		// check that school exists
		if (false == $db->objectExists('schools_school', $formvars['school'])) 
			{ $report .= "[*] Please choose a school from the list.<br/>\n"; }	

		if ('' == $report) {
			//--------------------------------------------------------------------------------------
			//	create the account
			//--------------------------------------------------------------------------------------
			$session->msg('Creating your account...', 'ok');

			$model = new Users_User();
			$model->UID = $kapenta->createUID();
			$model->school = $utils->cleanString($formvars['school']);
			$model->role = 'student';
			$model->grade = $utils->cleanString($formvars['grade']);
			$model->firstname = $utils->cleanString($formvars['firstname']);
			$model->surname = $utils->cleanString($formvars['surname']);
			$model->username = $utils->cleanString($formvars['username']);
			$model->password = sha1($formvars['pass1'] . $model->UID);
			$model->lang = 'en';
			$model->profile = '';
			$model->permissions = '';
			$model->lastOnline = $db->datetime();
			$model->createdOn = $db->datetime();
			$model->createdBy = 'admin';
			$model->save();

			//authUpdatePermissions();

			//--------------------------------------------------------------------------------------
			//	sign user in
			//--------------------------------------------------------------------------------------
			$session->user = $model->UID;
			$session->msg('You are now logged in.', 'ok');
			$user->load($model->UID);
			$page->do302('users/profile/'); // show user his profile

		} else {
			//--------------------------------------------------------------------------------------
			//	not enough info yet
			//--------------------------------------------------------------------------------------
			$report = "<b>Before you continue:</b><br/>\n" . $report . "<br/><br/>\n";
			$session->msg("<font color='red'>" . $report . "</font>", 'bad');
		}

	}

	//----------------------------------------------------------------------------------------------
	//	show page
	//----------------------------------------------------------------------------------------------

	if ($showPage == true) {
		$page->load('modules/users/actions/signup.page.php');
		foreach($formvars as $field => $value) { $page->blockArgs[$field] = $value; }
		$page->render();
	}

?>
