<?

//--------------------------------------------------------------------------------------------------
//*	stub installer, docgen does not have a database footprint as yet
//--------------------------------------------------------------------------------------------------

function docgen_install_module() {
	
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report [string]

function docgen_install_status_report() {
	global $user;
	if ('admin' != $user->role) { return false; }

	$report = '';
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$report .= "This module does not use the database at present.";

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
