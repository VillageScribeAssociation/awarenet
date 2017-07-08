<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/mysqliadmin.dbd.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Tags module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Tags module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function tags_install_module() {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Tags_Index table
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Index();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Tags_Tag table
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Tag();
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

function tags_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Index objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Index();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Tag objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Tags_Tag();
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
