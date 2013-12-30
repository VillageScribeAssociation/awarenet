<?

	require_once($kapenta->installPath . 'modules/tutorials/inc/assemble.inc.php');

//--------------------------------------------------------------------------------------------------
//*	install script for tutorials module (tutorial videos)
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the tutorials module
//--------------------------------------------------------------------------------------------------
//returns: html report, or false if not authorized [string][bool]

function tutorials_install_module() {
	global $user;
	global $db;
	global $kapenta;

	$report = '';
	
	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing tutorials Module</h3>\n";

	$fileNames = array("modules/tutorials/assets/awarenet_getting_started.mp4",
				"modules/tutorials/assets/awarenet_tutorial_2.mp4");
	tutorials_assemble($fileNames);
	
	$report .= "<b>assembled given files</b><br/>";


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

function tutorials_install_status_report() {
	global $user;
	global $kapenta;

	$report = '';

	if ('admin' != $user->role) { return false; }
	
	//use old fileExists so that we are compatible with old installations
	
	if ($kapenta->fileExists("modules/tutorials/assets/awarenet_getting_started.mp4") and
		$kapenta->fileExists("modules/tutorials/assets/awarenet_tutorial_2.mp4") ) {
		$report = '<!-- installed correctly -->';
	} else {
		$report = '<!-- tutorials NOT installed correctly -->';
	}

	return $report;
}

?>
