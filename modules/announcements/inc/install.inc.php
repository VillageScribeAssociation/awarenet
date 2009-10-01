<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/announcements/models/announcements.mod.php');

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
