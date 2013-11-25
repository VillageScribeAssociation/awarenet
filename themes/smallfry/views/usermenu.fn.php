<?

//--------------------------------------------------------------------------------------------------
//|	create the site's top menu bar according to context
//--------------------------------------------------------------------------------------------------

function theme_usermenu($args) {
	global $user;
	global $theme;
	global $session;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check user role and mobile status
	//----------------------------------------------------------------------------------------------
	$block = 'publicmenu.block.php';

	if (('public' != $user->role) && ('banned' != $user->role)) { $block = 'usermenu.block.php'; }

	$adminCl = '';
	if ('admin' == $user->role) {
		$adminCl = "<a href='%%serverPath%%admin/' class='menu'>Admin</a>";
	}

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('themes/' . $kapenta->defaultTheme . '/views/' . $block);
	$html = str_replace('%%adminConsoleLink%%', $adminCl, $html);
	$html = str_replace('%%userUID%%', $user->UID, $html);

	return $html;
}

?>
