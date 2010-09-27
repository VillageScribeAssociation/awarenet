<?

	require_once($kapenta->installPath . 'modules/notifications/models/notification.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show latest x notifications recieved by a given user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]
//opt: num - number of notifications to show (default 20) [string]

function notifications_list($args) {
	global $theme;

	global $db;
	$num = 20;	$html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }
	if ($db->objectExists('users', $args['userUID']) == false) { return false; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	$model = new NotificationQueue($args['userUID']);
	$noticeArray = array_reverse($model->notifications);

	foreach($noticeArray as $UID => $notice) {
		$notice['time'] = $db->datetime($notice['timestamp']) . ' (' . $notice['timestamp'] . ')';

		if ($noitice['imgUID'] != '') {
			$html .= $theme->replaceLabels($notice, $theme->loadBlock('modules/notifications/views/notice.block.php')); 
		} else { 
			$html .= $theme->replaceLabels($notice, $theme->loadBlock('modules/notifications/views/noticeimg.block.php'));
		}
	}

	return $html;
}


?>