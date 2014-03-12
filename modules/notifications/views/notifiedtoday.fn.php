<?

//--------------------------------------------------------------------------------------------------
//*	discover if a notification was sent today
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which raised notification [string]
//agr: refUID - UID of mobject which raised notification [string]
//agr: refEvent - name of event which caused this notification to be sent [string]
//returns: UID of last notification on success, empty string on failure [string]

function notifications_notifiedtoday($args) {
		global $kapenta;
		global $kapenta;
		global $session;


	//----------------------------------------------------------------------------------------------
	//	check arguments ans permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return "[[:users::plaselogin:]]"; }
	if (false == array_key_exists('refModule', $args)) { return 'err'; }
	if (false == array_key_exists('refModel', $args)) { return 'err'; }
	if (false == array_key_exists('refUID', $args)) { return 'err'; }
	if (false == array_key_exists('refEvent', $args)) { return 'err'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	$conditions[] = "refEvent='" . $kapenta->db->addMarkup($args['refEvent']) . "'";

	$by = "createdOn DESC";
	$limit = '1';

	$range = $kapenta->db->loadRange('notifications_notification', '*', $conditions, $by, $limit);

	foreach($range as $row) {
		$now = $kapenta->db->datetime();
		$today = substr($now, 0, 10);
		$nday = substr($row['createdOn'], 0, 10);
		$kapenta->session->msgAdmin("today: $today nday: $nday");
		if ($today == $nday) { return $row['UID']; }
	}

	return '';

}

?>
