<?

//--------------------------------------------------------------------------------------------------
//|	create the site's top menu bar according to context
//--------------------------------------------------------------------------------------------------

function home_usermenu($args) {
	global $kapenta;
	global $theme;
	global $session;

	//----------------------------------------------------------------------------------------------
	//	check user role and mobile status
	//----------------------------------------------------------------------------------------------
	$block = 'publicmenu.block.php';

	if (('public' != $kapenta->user->role) && ('banned' != $kapenta->user->role)) { $block = 'usermenu.block.php'; }

	$adminCl = '';
	if ('admin' == $kapenta->user->role) {
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
