<?

//--------------------------------------------------------------------------------------------------
//	install script for announcements modules
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//	install the gallery module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function announcements_install_module() {
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	$report .= "<h3>Installing Announcements Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	create announcement table if it does not exist, upgrade it if it does
	//------------------------------------------------------------------------------------------
	$model = new Announcement();
	$dbSchema = $model->initDbSchema();
	$report .= dbInstallTable($dbSchema);	

	//------------------------------------------------------------------------------------------
	//	done
	//------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//returns: HTML installation report [string]
// if installed correctly report will contain HTML comment <!-- installed correctly -->

function announcements_install_status_report() {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	//---------------------------------------------------------------------------------------------
	//	ensure that the announcement table exists and is correct
	//---------------------------------------------------------------------------------------------
	$installed = true;
	$installNotice = '<!-- table installed correctly -->';
	$model = new Announcement();
	$dbSchema = $model->initDbSchema();

	$report = dbGetTableInstallStatus($dbSchema);

	if (strpos($report, $installNotice) == false) { $installed = false; }

	if (true == $installed) { $report .= "<!-- module installed correctly -->"; }
	return $report;
}

//-------------------------------------------------------------------------------------------------
//	deprecated	// TODO: remove
//-------------------------------------------------------------------------------------------------

function install_announcements_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Announcement();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
