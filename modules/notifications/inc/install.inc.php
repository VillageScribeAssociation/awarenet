<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/notifications/models/notifications.mod.php');

function install_notifications_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new NotificationQueue();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
