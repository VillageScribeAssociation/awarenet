<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/projects/models/project.mod.php');
require_once($installPath . 'modules/projects/models/membership.mod.php');
require_once($installPath . 'modules/projects/models/projectrevision.mod.php');

function install_projects_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$report = '';

	//----------------------------------------------------------------------------------------------
	//	install projects table
	//----------------------------------------------------------------------------------------------
	$model = new Project();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	install projects membership table
	//----------------------------------------------------------------------------------------------
	$model = new ProjectMembership();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	install projects revision table
	//----------------------------------------------------------------------------------------------
	$model = new ProjectRevision();
	$report .= $model->install();

	return $report;
}

?>
