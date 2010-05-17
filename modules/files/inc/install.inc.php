<?

//--------------------------------------------------------------------------------------------------
//	installer for files module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/files/models/file.mod.php');

function install_files_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$f = new File();

	$report = '';
	$report .= $f->install();

	return $report;
}

?>
