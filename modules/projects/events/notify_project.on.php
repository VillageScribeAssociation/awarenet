<?

//--------------------------------------------------------------------------------------------------
//|	adds everyone who has commented on some object to a notification
//--------------------------------------------------------------------------------------------------
//arg: projectUID - UID of a project [string]
//arg: notificationUID - UID of notification [string]

function projects__cb_notify_project($args) {
	global $notifications;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('projectUID', $args)) { return false; }
	if (false == array_key_exists('notificationUID', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "projectUID='" . $kapenta->db->addMarkup($args['projectUID']) . "'";
	$conditions[] = "(role='admin' OR role='member')";

	$range = $kapenta->db->loadRange('projects_membership', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	add all members of this project
	//----------------------------------------------------------------------------------------------
	foreach ($range as $row) { $notifications->addUser($args['notificationUID'], $row['userUID']); }		

	return true;
}

?>
