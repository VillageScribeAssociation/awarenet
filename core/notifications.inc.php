<?

//--------------------------------------------------------------------------------------------------
//	core functions for sending notifications
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/notifications/models/notifications.mod.php');
require_once($installPath . 'modules/notifications/models/pagechannel.mod.php');

//==================================================================================================
//--------------------------------------------------------------------------------------------------
// 	USER NOTIFICATIONS
//--------------------------------------------------------------------------------------------------
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// send notification to specific user
//--------------------------------------------------------------------------------------------------

function notifyUser($userUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$model = new NotificationQueue($userUID);
	$model->addNotification($noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
}

//--------------------------------------------------------------------------------------------------
// send notification to an entire school
//--------------------------------------------------------------------------------------------------

function notifySchool($schoolUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$sql = "select UID from users where school='" . $schoolUID . "'";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		notifyUser($row['UID'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to a group
//--------------------------------------------------------------------------------------------------

function notifyGroup($groupUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$sql = "select userUID from groupmembers where groupUID='" . $groupUID . "'";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		notifyUser($row['userUID'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to users friends
//--------------------------------------------------------------------------------------------------

function notifyFriends($userUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$u = new Users($userUID);
	$friends = $u->getFriends();
	foreach($friends as $UID => $row) {
		notifyUser($row['friendUID'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to members of a project
//--------------------------------------------------------------------------------------------------

function notifyProject($projectUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$sql = "select * from projectmembers "
		 . "where projectUID='" . sqlMarkup($projectUID) . "' and role != 'asked'";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		notifyUser($row['userUID'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to admins of a project
//--------------------------------------------------------------------------------------------------

function notifyProjectAdmins($pUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$sql = "select * from projectmembers "
		 . "where projectUID='" . sqlMarkup($pUID) . "' and role='admin'";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		notifyUser($row['userUID'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to a school grade
//--------------------------------------------------------------------------------------------------

function notifyGrade($schoolUID, $grade, $nUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	$sql = "select * from users"
		 . " where school='" . sqlMarkup($schoolUID) . "' and grade='" . sqlMarkup($grade) . "'";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($sql)) {
		$row = sqlRMArray($row);
		notifyUser($row['UID'], $nUID, $from, $fromurl, $title, $content, $url, $imgUID);		
	}
}

//--------------------------------------------------------------------------------------------------
// send notification to everyone participating in a forum thread
//--------------------------------------------------------------------------------------------------

function notifyThread($threadUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	//----------------------------------------------------------------------------------------------
	//	notify original thread creator
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');
	$t = new ForumThread($threadUID);
	notifyUser($t->data['createdBy'], $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID);

	//----------------------------------------------------------------------------------------------
	//	notify everyone who's replied
	//----------------------------------------------------------------------------------------------
	$result = dbQuery("select * from forumreplies where thread='" . sqlMarkup($threadUID) . "'");
}

//==================================================================================================
//--------------------------------------------------------------------------------------------------
// 	PAGE NOTIFICATIONS
//--------------------------------------------------------------------------------------------------
//==================================================================================================

//--------------------------------------------------------------------------------------------------
// broadcast notification on an page channel
//--------------------------------------------------------------------------------------------------

function notifyChannel($channelID, $event, $data, $rebroadcast = true) {
	//----------------------------------------------------------------------------------------------
	//	send to locally subscribed clients
	//----------------------------------------------------------------------------------------------
	$model = new PageChannel('');	// don't load it yet
	if ($model->channelExists($channelID) == true) {
		$model->load($channelID);
		$model->broadcast($event, $data);
	}

	//----------------------------------------------------------------------------------------------
	//	broadcast to peer servers
	//----------------------------------------------------------------------------------------------
	if ($rebroadcast == true) { syncBroadcastNotification('self', $channelID, $event, $data); }
}

?>
