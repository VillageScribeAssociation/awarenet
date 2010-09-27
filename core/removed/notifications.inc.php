<?

//--------------------------------------------------------------------------------------------------
//*	core functions for sending notifications, deprecated
//--------------------------------------------------------------------------------------------------
//+ TODO: Notifications should now be sent by raising events on notifications module

//require_once($installPath . 'modules/notifications/models/notification.mod.php');
//require_once($installPath . 'modules/notifications/models/pagechannel.mod.php');
//require_once($installPath . 'modules/notifications/models/pageclient.mod.php');

//==================================================================================================
//--------------------------------------------------------------------------------------------------
// 	USER NOTIFICATIONS
//--------------------------------------------------------------------------------------------------
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//|	send notification to specific user
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]
//arg: noticeUID - UID of notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyUser($userUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyUser, use \$notifications->addUser()', 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	send notification to an entire school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of a school record [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifySchool($schoolUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifySchool, use \$notifications->addSchool()', 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	send notification to a group
//--------------------------------------------------------------------------------------------------
//arg: groupUID - UID of a group [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyGroup($groupUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyGroup, use \$notifications->addGroup()', 'bug');
}

//--------------------------------------------------------------------------------------------------
//| send notification to users friends
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyFriends($userUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyFriends, use \$notifications->addFriend()', 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	send notification to members of a project
//--------------------------------------------------------------------------------------------------
//arg: projectUID - UID of a project [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyProject($projectUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyProject, use \$notifications->addProject()', 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	send notification to admins of a project
//--------------------------------------------------------------------------------------------------
//arg: pUID - UID of a project [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyProjectAdmins($pUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$msg = 'method deleted: notifyProjectAdmins, use \$notifications->addProjectAdmins()';
	$session->msgAdmin($msg, 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	send notification to a school grade
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of a school [string]
//arg: grade - grade label [string]
//arg: nUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyGrade($schoolUID, $grade, $nUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyGrade, use \$notifications->addSchoolGrade()', 'bug');
}

//--------------------------------------------------------------------------------------------------
// send notification to everyone participating in a forum thread
//--------------------------------------------------------------------------------------------------
//arg: threadUID - UID of a forum thread [string]
//arg: noticeUID - UID of a notification [string]
//arg: from - who or what created this notification [string]
//arg: fromurl - link to user or entity responsible for event resulting in notification [string]
//arg: title - title of notification (html) [string]
//arg: content - body of notification (html) [string]
//arg: url - link to subject of notification [string]
//arg: imgUID - UID of an image for thumbnail [string]
//: imgUID this may allow or be replaced by image URL in future to remove dependancy

function notifyThread($threadUID, $noticeUID, $from, $fromurl, $title, $content, $url, $imgUID) {
	global $session;
	$session->msgAdmin('method deleted: notifyThread, use \$notifications object()', 'bug');
}

//==================================================================================================
//--------------------------------------------------------------------------------------------------
// 	PAGE NOTIFICATIONS
//--------------------------------------------------------------------------------------------------
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//| broadcast notification on an page channel
//--------------------------------------------------------------------------------------------------
//arg: channelID - label identifying a page channel; a block, chat window, etc [string]
//arg: event - channel dependant [string]
//arg: data - usually base 64 encoded [string]
//opt: rebroadcast - pass to peer servers if true [string]

function notifyChannel($channelID, $event, $data, $rebroadcast = true) {
	/*
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
	*/
}

//--------------------------------------------------------------------------------------------------
// subscribe a page to a channel before it's rendered
//--------------------------------------------------------------------------------------------------
//arg: channelID - label identifying a page channel; a block, chat window, etc [string]

function notifySubscribe($channelID) {
	global $page;
	/*
	$model = new PageClient($page->UID);
	$model->subscribe($channelID);
	*/
}

?>
