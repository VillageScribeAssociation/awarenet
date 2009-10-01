<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/users/models/users.mod.php');

function install_users_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Users();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
