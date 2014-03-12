<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Notification object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Notifications_Notification object [string]//opt: notificationUID - UID of a Notifications_Notification object, overrides UID [string]
function notifications_editnotification($args) {
		global $kapenta;
		global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if ('admin' != $kapenta->user->role) { return 'admins only'; }
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('notificationUID', $args)) { $raUID = $args['notificationUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Notifications_Notification($raUID);	//% the object we're editing [object:Notifications_Notification]

	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('notifications', 'notifications_notification', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/notifications/views/editnotification.block.php');
	$labels = $model->extArray();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
