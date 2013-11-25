<?php

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/cache/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for cache module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Cache module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function cache_install_module() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade cache_entry table
	//----------------------------------------------------------------------------------------------
	$model = new Cache_Entry();
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

function cache_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';				//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Entry objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Cache_Entry();
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
