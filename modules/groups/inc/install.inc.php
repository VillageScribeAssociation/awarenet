<?

//--------------------------------------------------------------------------------------------------
//	installer for groups module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/groups/models/groups.mod.php');

function install_groups_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Group();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
