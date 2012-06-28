<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	site login form
//--------------------------------------------------------------------------------------------------
//opt: redirectSelf - redirect back to the same page after login (yes|no) [string]
//opt: return - URL to redirect to [string]
//opt: tb - wrap in titlebox, default is no (yes|no) [string]
//TODO: sanitize return URLs

function users_loginform($args) { 
	global $user;
	global $theme;

	$redirectSelf = 'no';		//%	return to the current page after login (yes|no) [string]
	$tb = 'no';					//%	wrap in titlebox block [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' != $user->role) { return ''; }
	if (true == array_key_exists('redirectSelf', $args)) { $redirectSelf = $args['redirectSelf']; }
	if (true == array_key_exists('tb', $args)) { $tb = $args['tb']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/loginform.block.php');
	$labels['redirectUrl'] = '';
		
	if ('yes' == $redirectSelf) {
		$redirectUrl = $_SERVER['REQUEST_URI'];
		if ('/' == substr($redirectUrl, 0, 1)) { $redirectUrl = substr($redirectUrl, 1); }
		$labels['redirectUrl'] = "<input type='hidden' name='redirect' value='$redirectUrl' />";
	}

	if (true == array_key_exists('return', $args)) {
		$redirectUrl = $args['return'];
		$labels['redirectUrl'] = "<input type='hidden' name='redirect' value='$redirectUrl' />";
	}

	$html = $theme->replaceLabels($labels, $block);

	if ('yes' == $tb) {
		$html = ''
		 . "[[:theme::navtitlebox::label=Log In::toggle=divLogIn:]]\n"
		 . "<div id='divLogIn'>\n"
		 . $html
		 . "</div>\n"
		 . "<div class='foot'></div>\n"
		 . '<br/>';
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
