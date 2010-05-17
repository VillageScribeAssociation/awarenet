<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/schools/models/school.mod.php');

function install_schools_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new School();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
