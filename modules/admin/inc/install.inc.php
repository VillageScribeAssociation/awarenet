<?

//--------------------------------------------------------------------------------------------------
//*	install scripts for admin module
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	install the admin module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function admin_install_module() {
	global $user;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing Admin Module</h3>\n";

	//------------------------------------------------------------------------------------------
	//	admin module has no database presence at now
	//------------------------------------------------------------------------------------------
	$report .= "Admin module has no objects serialized to the database.";

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

function admin_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }	// only admins can do this
	$installed = true;

	$report = "<p>Admin module requires no further configuration.</p>";

	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

?>
