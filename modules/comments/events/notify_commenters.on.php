<?

//--------------------------------------------------------------------------------------------------
//|	adds everyone who has commented on some object to a notification
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object [string]
//arg: refUID - UID of object which may have comments [string]
//arg: notificationUID - UID of notification [string]

function comments__cb_notify_commenters($args) {
	global $notifications;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return false; }
	if (false == array_key_exists('refModel', $args)) { return false; }
	if (false == array_key_exists('refUID', $args)) { return false; }
	if (false == array_key_exists('notificationUID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";

	$range = $kapenta->db->loadRange('comments_comment', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	add everyone who commented to notification
	//----------------------------------------------------------------------------------------------
	foreach($range as $item) {
		$notifications->addUser($args['notificationUID'], $item['createdBy']);
	}

	return true;
}

?>
