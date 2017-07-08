<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'core/dbdriver/mysqliadmin.dbd.php');
	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for Images module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Images module
//--------------------------------------------------------------------------------------------------
//returns: html report or false if not authorized [string][bool]

function images_install_module() {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade Images_Image table
	//----------------------------------------------------------------------------------------------
	$model = new Images_Image();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	import any records from previous images table
	//----------------------------------------------------------------------------------------------
	$rename = array('record');
	$count = $dba->copyAll('images', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'images' table.</b><br/>";

	//----------------------------------------------------------------------------------------------
	//	create file associations
	//----------------------------------------------------------------------------------------------

	$assoc = array('jpg', 'jpeg', 'png', 'gif');
	foreach($assoc as $ext) {
		if ('images' != $kapenta->registry->get('live.file.' . $ext)) {
			$kapenta->registry->set('live.file.' . $ext, 'images');
			$report .= "<b>Added file association:</b> $ext<br/>";
		}
	}

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

function images_install_status_report() {
	global $kapenta;
	global $kapenta;

	if ('admin' != $kapenta->user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Image objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Images_Image();
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
