<?

//--------------------------------------------------------------------------------------------------
//*	set theme preferences of current user
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { $kapenta->page->do403(); }
	
	foreach($_POST as $key => $value) {
		//echo "$key := $value <br/>\n";
		switch($key) {
			case 'background':
				$size = 'full';
				if (true == array_key_exists('transform', $_POST)) {
					$size = $_POST['transform'];		//TODO: sanitize this
				}

				if ('full' == $size) {
					$kapenta->user->set('ut.i.background', 'images/' . $size . '/' . $value);
				} else {
					$kapenta->user->set('ut.i.background', 'images/s_' . $size . '/' . $value);
				}				
				$kapenta->session->msg('Setting user background.');
				break;

			case 'theme_c_darkest':		$kapenta->user->set('ut.c.darkest', '#' . $value);		break;
			case 'theme_c_darker':		$kapenta->user->set('ut.c.darker', '#' . $value);		break;
			case 'theme_c_dark':		$kapenta->user->set('ut.c.dark', '#' . $value);			break;
			case 'theme_c_medium':		$kapenta->user->set('ut.c.medium', '#' . $value);		break;
			case 'theme_c_light':		$kapenta->user->set('ut.c.light', '#' . $value);			break;
			case 'theme_c_lighter':		$kapenta->user->set('ut.c.lighter', '#' . $value);		break;
			case 'theme_c_lightest':	$kapenta->user->set('ut.c.lightest', '#' . $value);		break;
			case 'theme_c_link':		$kapenta->user->set('ut.c.link', '#' . $value);			break;
			case 'theme_c_background':	$kapenta->user->set('ut.c.background', '#' . $value);	break;
			case 'theme_c_action':		$kapenta->user->set('ut.c.action', '#' . $value);		break;
			case 'theme_c_text':		$kapenta->user->set('ut.c.text', '#' . $value);			break;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to 'my account'
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do302('users/myaccount/');


?>
