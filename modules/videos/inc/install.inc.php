<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');
	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Videos module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Videos module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function videos_install_module() {
	global $db, $user;
	if ('admin' != $user->role) { return false; }
	$dba = new KDBAdminDriver();
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Videos_Gallery table
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Gallery();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Videos_Video table
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Video();
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

function videos_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$dba = new KDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Gallery objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Gallery();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Video objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Videos_Video();
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
