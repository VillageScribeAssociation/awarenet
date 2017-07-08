<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/mysqliadmin.dbd.php');
	require_once($kapenta->installPath . 'modules/live/models/chat.mod.php');
	require_once($kapenta->installPath . 'modules/live/models/mailbox.mod.php');
	require_once($kapenta->installPath . 'modules/live/models/trigger.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Live module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Live module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function live_install_module() {
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Live_Chat table
	//----------------------------------------------------------------------------------------------
	$model = new Live_Chat();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Live_Mailbox table
	//----------------------------------------------------------------------------------------------
	$model = new Live_Mailbox();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Live_Trigger table
	//----------------------------------------------------------------------------------------------
	$model = new Live_Trigger();
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

function live_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Chat objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Live_Chat();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Mailbox objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Live_Mailbox();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Trigger objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Live_Trigger();
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
