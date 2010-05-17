<?

//--------------------------------------------------------------------------------------------------
//|	this is the top menu bar
//--------------------------------------------------------------------------------------------------

function home_menu($args) {
	global $user;
	$labels = array();

	$labels['menuAdmin'] = "[[:theme::menu::label=Admin::link=/admin/::alt=admin console:]]\n";
	$labels['menuLogin'] = "[[:theme::menu::label=Log in::link=/users/login/::alt=log in:]]\n";
	$labels['menuLogout'] = "[[:theme::menu::label=Log out::link=/users/logout/::alt=log out:]]\n";

	if ($user->data['ofGroup'] != 'admin') { $labels['menuAdmin'] = ''; }
	if ($user->data['ofGroup'] == 'public') { $labels['menuLogout'] = ''; }
	if ($user->data['ofGroup'] != 'public') { $labels['menuLogin'] = ''; }

	return replaceLabels($labels, loadBlock('modules/home/views/menu.block.php'));
}


?>
