<?

	require_once($kapenta->installPath . 'modules/home/models/static.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for home module (static pages)
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the Home_Static module
//--------------------------------------------------------------------------------------------------
//returns: html report, or false if not authorized [string][bool]

function home_install_module() {
	global $user;
	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing Home_Static Module</h3>\n";
	$dba = new KDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create Home_Static table if it does not exist, upgrade it if it does
	//----------------------------------------------------------------------------------------------
	$model = new Home_Static();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);	

	//----------------------------------------------------------------------------------------------
	//	import any records from previous static table
	//----------------------------------------------------------------------------------------------
	$rename = array('recordAlias' => 'alias');
	$count = $dba->copyAll('static', $dbSchema, $rename); 
	$report .= "<b>moved $count records from 'static' table.</b><br/>";

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

function home_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$dba = new KDBAdminDriver();
	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Static objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new Home_Static();
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
