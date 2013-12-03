<?

//--------------------------------------------------------------------------------------------------
//*	install scripts for WYSWYG editor
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	install the WYSWYG editor module
//--------------------------------------------------------------------------------------------------
//returns: html report [string]

function editor_install_module() {
	global $user;

	if ('admin' != $user->role) { return false; }	// only admins can do this

	$report = "<h3>Installing WYSWYG Editor</h3>\n";

	//------------------------------------------------------------------------------------------
	//	admin module has no database presence at now
	//------------------------------------------------------------------------------------------
	$report .= "WYSWYG editor module has no objects serialized to the database.";

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

function editor_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }	// only admins can do this
	$installed = true;

	$report = "<p>WYSWYG editor module requires no further configuration.</p>";

	if (true == $installed) { $report .= "<!-- installed correctly -->"; }

	return $report;
}

?>
