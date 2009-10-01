<?

	require_once($installPath . 'modules/notifications/models/notifications.mod.php');

//--------------------------------------------------------------------------------------------------
//	show latest x notifications recieved by a given user
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of a user
// * $args['num'] = how many to show

function notifications_list($args) {
	$num = 20;	$html = '';
	if (array_key_exists('userUID', $args) == false) { return false; }
	if (dbRecordExists('users', $args['userUID']) == false) { return false; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	$model = new NotificationQueue($args['userUID']);
	$noticeArray = array_reverse($model->notifications);

	foreach($noticeArray as $UID => $notice) {
		$notice['time'] = mysql_datetime($notice['timestamp']);

		if ($noitice['imgUID'] != '') {
			$html .= replaceLabels($notice, loadBlock('modules/notifications/views/notice.block.php')); 
		} else { 
			$html .= replaceLabels($notice, loadBlock('modules/notifications/views/noticeimg.block.php'));
		}
	}

	return $html;
}


?>