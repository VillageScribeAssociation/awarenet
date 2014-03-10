<?

//--------------------------------------------------------------------------------------------------
//|	create the site's top menu bar according to context
//--------------------------------------------------------------------------------------------------

function theme_usermenu($args) {
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check user role and mobile status
	//----------------------------------------------------------------------------------------------
	$block = 'publicmenu.block.php';

	if (('public' != $kapenta->user->role) && ('banned' != $kapenta->user->role)) { 
        $block = 'usermenu.block.php'; 
    }

	$adminCl = '';
	if ('admin' == $kapenta->user->role) {
		$adminCl = "<a href='%%serverPath%%admin/' class='menu'>Admin</a>";
	}

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $kapenta->theme->loadBlock('themes/' . $kapenta->defaultTheme . '/views/' . $block);
	$html = str_replace('%%adminConsoleLink%%', $adminCl, $html);
	$html = str_replace('%%userUID%%', $kapenta->user->UID, $html);

	return $html;
}

?>
