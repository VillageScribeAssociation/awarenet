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

					$msg = "[[:theme::navtitlebox::label=Notice:]]"
						 . "<div class='inlinequote'><p>"
						 . "<img src='%%serverPath%%themes/clockface/images/info.png' "
						 . "class='infobutton' width='18' height='18' />&nbsp;&nbsp;"
						 . "You are now logged in.</p></div>\n";


					$_SESSION['sUser'] = $row['username'];
					$_SESSION['sUserUID'] = $row['UID'];

					$_SESSION['sMessage'] .= $msg;
					$user->load($row['UID']);
					do302('notifications/'); // user notifications

				}

			}
		}

		//------------------------------------------------------------------------------------------
		//	cound not authenticate
		//------------------------------------------------------------------------------------------
		
		$msg = "[[:theme::navtitlebox::label=Notice:]]"
			 . "<div class='inlinequote'><p>"
			 . "<img src='%%serverPath%%themes/clockface/images/btn-del.png' "
			 . "class='infobutton' width='18' height='18' />&nbsp;&nbsp;"
			 . "Username or password not recognised, you are not logged in.</p></div>\n";

		$_SESSION['sMessage'] .= $msg;
								

	}

//--------------------------------------------------------------------------------------------------
//	display form
//--------------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/users/actions/login.page.php');
	$page->render();

?>
