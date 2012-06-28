<?

//--------------------------------------------------------------------------------------------------
//*	set theme preferences of current user
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { $page->do403(); }
	
	foreach($_POST as $key => $value) {
		//echo "$key := $value <br/>\n";
		switch($key) {
			case 'background':
				$size = 'full';
				if (true == array_key_exists('transform', $_POST)) {
					$size = $_POST['transform'];		//TODO: sanitize this
				}

				$user->set('ut.i.background', 'images/' . $size . '/' . $value);
				$session->msg('Setting user background.');
				break;

			case 'theme_c_darkest':		$user->set('ut.c.darkest', '#' . $value);		break;
			case 'theme_c_darker':		$user->set('ut.c.darker', '#' . $value);		break;
			case 'theme_c_dark':		$user->set('ut.c.dark', '#' . $value);			break;
			case 'theme_c_medium':		$user->set('ut.c.medium', '#' . $value);		break;
			case 'theme_c_light':		$user->set('ut.c.light', '#' . $value);			break;
			case 'theme_c_lighter':		$user->set('ut.c.lighter', '#' . $value);		break;
			case 'theme_c_lightest':	$user->set('ut.c.lightest', '#' . $value);		break;
			case 'theme_c_link':		$user->set('ut.c.link', '#' . $value);			break;
			case 'theme_c_background':	$user->set('ut.c.background', '#' . $value);	break;
			case 'theme_c_action':		$user->set('ut.c.action', '#' . $value);		break;
			case 'theme_c_text':		$user->set('ut.c.text', '#' . $value);			break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to 'my account'
	//----------------------------------------------------------------------------------------------
	$page->do302('users/myaccount/');


?>
