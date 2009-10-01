<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/images/models/image.mod.php');

function install_images_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$im = new Image();

	$report = '';
	$report .= $im->install();

	return $report;
}

?>
