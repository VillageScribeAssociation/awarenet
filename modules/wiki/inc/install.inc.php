<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/category.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/mwimport.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Wiki module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Wiki module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function wiki_install_module() {
	global $db, $user;
	if ('admin' != $user->role) { return false; }
	$dba = new KDBAdminDriver();
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Wiki_Article table
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Article();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous Articles table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('wiki', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'wiki' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Wiki_Category table
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Category();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Wiki_Revision table
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Revision();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Wiki_MWImport table
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_MWImport();
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

function wiki_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$dba = new KDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Article objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Article();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Category objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Category();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Revision objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Revision();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores MWImport objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_MWImport();
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
