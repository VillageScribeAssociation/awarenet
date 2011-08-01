<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/popular/models/ladder.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Popular module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Popular module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function popular_install_module() {
	global $user;
	if ('admin' != $user->role) { return ''; }

	$dba = new KDBAdminDriver();
	$report = '';				//% return value [string:html]
	//----------------------------------------------------------------------------------------------
	//	create or upgrade popular_ladder table
	//----------------------------------------------------------------------------------------------
	$model = new Popular_Ladder();
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

function popular_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$report = '';				//%	return value [string:html]
	$dba = new KDBAdminDriver();
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Ladder objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Popular_Ladder();
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
