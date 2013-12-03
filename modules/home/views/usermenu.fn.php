<?

//--------------------------------------------------------------------------------------------------
//|	create the site's top menu bar according to context
//--------------------------------------------------------------------------------------------------

function home_usermenu($args) {
	global $user;
	global $theme;
	global $session;

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
	$html = $theme->loadBlock('modules/home/views/' . $block);
	$html = str_replace('%%adminConsoleLink%%', $adminCl, $html);

	return $html;
}

?>
