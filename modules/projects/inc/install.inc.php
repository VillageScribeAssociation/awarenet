<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Projects module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Projects module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function projects_install_module() {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Projects_Membership table
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Membership();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous static table
	//----------------------------------------------------------------------------------------------
	$rename = array();
	$count = $dba->copyAll('projectmembers', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'projectmembers' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Projects_Project table
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('projects', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'projects' table.</b><br/>";


	/*
	//----------------------------------------------------------------------------------------------
	//	create or upgrade Projects_Revision table
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: TODO Remove this when safe to do so
	$model = new Projects_Revision();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);
	*/

	//----------------------------------------------------------------------------------------------
	//	import any records from previous static table
	//----------------------------------------------------------------------------------------------
	$rename = array();
	$count = $dba->copyAll('projectrevisions', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'projectrevisions' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Projects_Section table
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Section();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Projects_Change table
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Change();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function projects_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Projects_Membership objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Membership();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Projects_Project objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	/*
	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Projects_Revision objects exists and is correct
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: TODO Remove this when safe to do so
	$model = new Projects_Revision();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;
	*/

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Projects_Section objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Section();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;


	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Projects_Change objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Change();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
