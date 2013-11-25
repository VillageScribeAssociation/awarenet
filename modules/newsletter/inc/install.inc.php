<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/adunit.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/category.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/notice.mod.php');
	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Newsletter module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Newsletter module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function newsletter_install_module() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return ''; }

	$dba = $kapenta->getDBAdminDriver();
	$report = '';				//% return value [string:html]

	//----------------------------------------------------------------------------------------------
	//	create or upgrade newsletter_adunit table
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Adunit();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade newsletter_category table
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Category();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade newsletter_edition table
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Edition();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade newsletter_notice table
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Notice();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade newsletter_subscription table
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Subscription();
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
//returns: HTML installation status report or empty string if not authorized [string]

function newsletter_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';				//%	return value [string:html]
	$dba = $kapenta->getDBAdminDriver();
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Adunit objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Adunit();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Category objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Category();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Edition objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Edition();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Notice objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Notice();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Subscription objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Newsletter_Subscription();
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
