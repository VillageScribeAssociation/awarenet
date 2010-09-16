<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	site login form
//--------------------------------------------------------------------------------------------------
//opt: redirectSelf - redirect back to the same page after login (yes|no) [string]

function users_loginform($args) { 
	global $user, $theme;
	$html = '';					//%	return value [string]
	$redirectSelf = 'no';

	if ('public' != $user->role) { return '(you are already logged in)'; }
	if (true == array_key_exists('redirectSelf', $args)) 
		{ if ('yes' == strtolower($args['redirectSelf'])) { $redirectSelf = 'yes'; } }

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

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
