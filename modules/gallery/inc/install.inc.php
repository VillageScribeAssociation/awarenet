<?

//--------------------------------------------------------------------------------------------------
//	installer for gallery module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/gallery/models/gallery.mod.php');

function install_gallery_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }
	$model = new Gallery();
	$report = $model->install();
	return $report;
}

?>
