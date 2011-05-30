<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a single notification
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a notification [string]
//opt: notificationUID - overrides UID [string]

function notifications_show($args) {
	global $user, $theme;
	$html = '';							//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('notificationUID', $args))
		{ $args['UID'] = $args['notificationUID']; }

	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Notifications_Notification($args['UID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('notifications', 'notifications_notification', 'show', $model->UID)) 
		{ return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/notifications/views/show.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	
	return $html;

}

?>
