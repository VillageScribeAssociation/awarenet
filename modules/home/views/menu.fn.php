<?

//--------------------------------------------------------------------------------------------------
//|	this is the top menu bar
//--------------------------------------------------------------------------------------------------

function home_menu($args) {
	global $theme;

	global $user;
	$labels = array();

	$labels['menuAdmin'] = "[[:theme::menu::label=Admin::link=/admin/::alt=admin console:]]\n";
	$labels['menuLogin'] = "[[:theme::menu::label=Log in::link=/users/login/::alt=log in:]]\n";
	$labels['menuLogout'] = "[[:theme::menu::label=Log out::link=/users/logout/::alt=log out:]]\n";

	if ('admin' != $user->role) { $labels['menuAdmin'] = ''; }
	if ('public' == $user->role) { $labels['menuLogout'] = ''; }
	if ('public' != $user->role) { $labels['menuLogin'] = ''; }

	return $theme->replaceLabels($labels, $theme->loadBlock('modules/home/views/menu.block.php'));
}


?>