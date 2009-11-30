<?

//--------------------------------------------------------------------------------------------------
//	login form
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	handle submissions
//--------------------------------------------------------------------------------------------------

	if ((array_key_exists('action', $_POST)) AND ($_POST['action'] == 'login')) {

		//------------------------------------------------------------------------------------------
		//	authenticate the user
		//------------------------------------------------------------------------------------------

		$username = sqlMarkup($_POST['user']);
		$password = sqlMarkup($_POST['pass']);

		$sql = "select * from users where username='" . $username . "'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) {
			$row = dbFetchAssoc($result);
			if ($row['password'] == sha1($password . $row['UID'])) {

				if ($row['ofGroup'] == 'banned') {
					//------------------------------------------------------------------------------
					//	details correct, but user is banhammered
					//------------------------------------------------------------------------------
					$_SESSION['sMessage'] .= "You have been banned, you are not logged in.<br/>\n";
					do302(''); // homepage

				} else {

					//------------------------------------------------------------------------------
					//	feedback and redirect
					//------------------------------------------------------------------------------

					$_SESSION['sUser'] = $row['username'];
					$_SESSION['sUserUID'] = $row['UID'];
					$_SESSION['sMessage'] .= "You are now logged in.<br/>\n";
					$user->load($row['UID']);
					do302('notifications/'); // user notifications

				}

			}
		}

		//------------------------------------------------------------------------------------------
		//	cound not authenticate
		//------------------------------------------------------------------------------------------
		
		$_SESSION['sMessage'] .= "Username or password not recognised, you are not logged in.<br/>\n";

	}

//--------------------------------------------------------------------------------------------------
//	display form
//--------------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/users/actions/login.page.php');
	$page->render();

?>
