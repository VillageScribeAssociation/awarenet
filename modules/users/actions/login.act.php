<?
	if ($kapenta->moduleExists( 'ldaplogin') ) {
		require_once($kapenta->installPath . 'modules/ldaplogin/actions/ldap.act.php');
	}

//--------------------------------------------------------------------------------------------------
//*	login form
//--------------------------------------------------------------------------------------------------
//+	TODO: make the landing page after login a setting which admins can change

	//----------------------------------------------------------------------------------------------
	//	handle submissions
	//----------------------------------------------------------------------------------------------
	if ((array_key_exists('action', $_POST)) AND ($_POST['action'] == 'login')) {

		//------------------------------------------------------------------------------------------
		//	authenticate the user
		//------------------------------------------------------------------------------------------
		$username = $kapenta->db->addMarkup($_POST['user']);
		$password = $kapenta->db->addMarkup($_POST['pass']);

		$range = $kapenta->db->loadRange('users_user', '*', array("username='" . $username . "'"));
		// ^ "select * from Users_User where username='" . $username . "'";

		if (count($range) > 0) {
			$row = array_shift($range);									//%	first row [array]
			if ($row['password'] == sha1($password . $row['UID'])) {

				if ($row['role'] == 'banned') {
					//------------------------------------------------------------------------------
					//	details correct, but user is banhammered
					//------------------------------------------------------------------------------
					$kapenta->session->msg('You have been banned, you are not logged in.', 'no');
					$kapenta->page->do302(''); 									// redirect to homepage

				} else {

					//------------------------------------------------------------------------------
					//	feedback and redirect
					//------------------------------------------------------------------------------
					$kapenta->session->set('user', $row['UID']);					//	set current user UID
					$kapenta->session->set('role', $row['role']);				//	set current user role
					$kapenta->session->msg('You are now logged in.', 'ok');
					$session = new Users_Session();

					$kapenta->user->loadArray($row);

					if (true == array_key_exists('redirect', $_POST)) { 
						$kapenta->page->do302($_POST['redirect']); 				// retry action after login
					} else {
						//--------------------------------------------------------------------------
						//	user is logged in, raise event and redirect
						//--------------------------------------------------------------------------
						$args = array('userUID' => $row['UID']);
						$kapenta->raiseEvent('*', 'users_login', $args);
						$kapenta->page->do302('notifications/'); 				// default landing page
					}

				}

			}
		} else {
			if ($kapenta->moduleExists( 'ldaplogin') ) {
				if(ldaplogin_check($username, $password)) {
					//LOAD first login screen
					$_SESSION['ldaplogin_username'] = $username;
					$_SESSION['ldaplogin_password'] = $password; 
					$kapenta->page->do302('ldaplogin/signup'); 				// redirect to account creation page
				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	cound not authenticate
		//------------------------------------------------------------------------------------------
		$kapenta->session->msg('Username or password not recognised, you are not logged in.', 'no');

	}

//--------------------------------------------------------------------------------------------------
//	display form
//--------------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/users/actions/login.page.php');
	$kapenta->page->render();

?>
