<?

//--------------------------------------------------------------------------------------------------
//	install scripts for chart widget
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	install the chart widget module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function chart_install_module() {
	global $user;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report .= "<h3>Installing Charts</h3>\n";

	//------------------------------------------------------------------------------------------
	//	admin module has no database presence at now
	//------------------------------------------------------------------------------------------
	$report .= "Charts module has no objects serialized to the database.";

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

function chart_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }	// only admins can do this
	$installed = true;

	$report = "<p>Charts module requires no further configuration.</p>";

	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

?>
