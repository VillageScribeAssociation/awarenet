<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');
	require_once($kapenta->installPath . 'modules/revisions/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Revisions module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Revisions module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function revisions_install_module() {
	global $db, $user;
	if ('admin' != $user->role) { return false; }
	$dba = new KDBAdminDriver();
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Revisions_Deleted table
	//----------------------------------------------------------------------------------------------
	$model = new Revisions_Deleted();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Revisions_Revision table
	//----------------------------------------------------------------------------------------------
	$model = new Revisions_Revision();
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

function revisions_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$dba = new KDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Deleted objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Revisions_Deleted();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Revision objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Revisions_Revision();
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
