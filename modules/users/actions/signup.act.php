<?
	require_once($kapenta->installPath . 'modules/picturelogin/inc/picturelogin.php');

//--------------------------------------------------------------------------------------------------
//*	page for signing up users
//--------------------------------------------------------------------------------------------------
//TODO: tidy this up, add settings and permissions appropriate to this

	//----------------------------------------------------------------------------------------------
	//	check if user user is authorized to create new accounts
	//----------------------------------------------------------------------------------------------

	if (('no' == $kapenta->registry->get('users.allowpublicsignup')) && ('admin' != $kapenta->user->role)) {
		$kapenta->session->msg('Public signup has been disabled.', 'bad');
		$kapenta->page->do403('Not authorized.');
	}

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

		if ('' != $kapenta->user->getUserUID($formvars['username'])) 
			{ $report .= "[*] Username is already taken.<br/>\n"; }	

		// check if user is already registered
		$sql = "select * from users_user"
			 . " where lower(username)='" . $kapenta->db->addMarkup(strtolower($formvars['username'])) ."'";

		$result = $kapenta->db->query($sql);

		if ($kapenta->db->numRows($result) != 0) { 
			$report .= "[*] The username <i>" . $formvars['username']. "</i> has already been "
					 . "registered, please choose another.<br/>\n"; 
		}	

		// check that school exists
		if (false == $kapenta->db->objectExists('schools_school', $formvars['school'])) 
			{ $report .= "[*] Please choose a school from the list.<br/>\n"; }	

		if ('' == $report) {
			//--------------------------------------------------------------------------------------
			//	create the account
			//--------------------------------------------------------------------------------------
			$kapenta->session->msg('Creating your account...', 'ok');

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
			$model->lastOnline = $kapenta->db->datetime();
			$model->createdOn = $kapenta->db->datetime();
			$model->createdBy = 'admin';
			$report = $model->save();

			if ('' == $report) {
				//----------------------------------------------------------------------------------
				//	user account created, sign user in and redirect to profile
				//----------------------------------------------------------------------------------
				$kapenta->session->user = $model->UID;
				$kapenta->session->set('user', $model->UID);					//	set current user UID
				$kapenta->session->set('role', $model->role);				//	set current user role
				$kapenta->session->msg('You are now logged in.', 'ok');
				$kapenta->user->load($model->UID);
				$kapenta->page->do302('users/profile/');			// show user his profile

			} else {
				$kapenta->session->msg('Could not create account:<br/>' . $report, 'bad');
				$kapenta->page->do302('users/signup/');			// back to signup form
			}

		} else {
			//--------------------------------------------------------------------------------------
			//	not enough info yet
			//--------------------------------------------------------------------------------------
			$report = "<b>Before you continue:</b><br/>\n" . $report . "<br/><br/>\n";
			$kapenta->session->msg("<font color='red'>" . $report . "</font>", 'bad');
		}

	}

	$style = getPictureLoginStyle();
	$script = getPictureLoginScript();

	//----------------------------------------------------------------------------------------------
	//	show page
	//----------------------------------------------------------------------------------------------

	if ($showPage == true) {
		$kapenta->page->load('modules/users/actions/signup.page.php');
		foreach($formvars as $field => $value) { $kapenta->page->blockArgs[$field] = $value; }
		$kapenta->page->blockArgs['head'] = '<style>' . $style . '</style>' . $script;
		$kapenta->page->render();
	}

?>
