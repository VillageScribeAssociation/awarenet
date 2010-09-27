<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');
require_once($kapenta->installPath . 'modules/notifications/models/pagechannel.mod.php');
require_once($kapenta->installPath . 'modules/notifications/models/pageclient.mod.php');

function install_notifications_module() {
	global $installPath;
	global $user;
	if ('admin' != $user->role) { return false; }

	$report = '';

	$model = new NotificationQueue();
	$report .= $model->install();

	$model = new PageClient();
	$report .= $model->install();

	$model = new PageChannel();
	$report .= $model->install();

	return $report;
}

?>
