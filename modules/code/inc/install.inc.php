<?php

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/mysqliadmin.dbd.php');
	require_once($kapenta->installPath . 'modules/code/models/bug.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/userindex.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Code module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Code module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function code_install_module() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_bug table
	//----------------------------------------------------------------------------------------------
	$model = new Code_Bug();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_file table
	//----------------------------------------------------------------------------------------------
	$model = new Code_File();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_package table
	//----------------------------------------------------------------------------------------------
	$model = new Code_Package();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_revision table
	//----------------------------------------------------------------------------------------------
	$model = new Code_Revision();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_userindex table
	//----------------------------------------------------------------------------------------------
	$model = new Code_UserIndex();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade code_change table
	//----------------------------------------------------------------------------------------------
	$model = new Code_Change();
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

function code_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';											//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Bug objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_Bug();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores File objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_File();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Package objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_Package();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Revision objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_Revision();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores UserIndex objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_UserIndex();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Code_Change objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Code_Change();
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
