<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/twitter/models/tweet.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Twitter module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Twitter module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function twitter_install_module() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';												//%	return value [string]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Twitter_Tweet table
	//----------------------------------------------------------------------------------------------
	$model = new Twitter_Tweet();
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

function twitter_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';												//%	return value [string]
	$installNotice = '<!-- table installed correctly -->';		//%	scripts look for this [string]
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Tweet objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Twitter_Tweet();
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
