<?

	require_once($installPath . 'modules/notifications/models/notification.mod.php');
	require_once($installPath . 'modules/notifications/models/userindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object representing the notifications system
//--------------------------------------------------------------------------------------------------
//TODO: try remove as many methods from this as possible

class KNotifications {

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KNotification() {
		// do nothing for now
	}

	//----------------------------------------------------------------------------------------------
	//.	create a new notification
	//----------------------------------------------------------------------------------------------
	//arg: title - title of notification (html) [string]
	//arg: content - body of notification (html) [string]
	//arg: url - link to subject of notification [string]
	//arg: imgUID - UID of an image for thumbnail [string]
	//returns: UID of new notification, or false on failure [string][bool]

	function create($refModule, $refModel, $refUID, $title, $content, $url = '') {
		$model = new Notifications_Notification();
		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->title = $title;
		$model->content = $content;
		$model->refUrl = $url;
		$report = $model->save();
		if ('' == $report) { return $model->UID; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	count notifications made about some object
	//----------------------------------------------------------------------------------------------

	function count($refModule, $refModel, $refUID) {
		global $db;
		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";
		$count = $db->countRange('Notifications_Notification', $conditions);
		return $count;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a user to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function addUser($notificationUID, $userUID) {
		$model = new Notifications_UserIndex();
		$model->notificationUID = $notificationUID;
		$model->userUID = $userUID;
		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add all admin users to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//returns: true on success, false on failure [bool]

	function addAdmins($notificationUID) {
		global $db;
		$allOk = true;		//%	return value [bool]

		$range = $db->loadRange('Users_User', '*', array("role='admin'"));
		foreach($range as $row) 
			{ if (false == $this->addUser($notificationUID, $row['UID'])) { $allOk = false; } }

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	add an entire school to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Schools_School object [string]
	
	function addSchool($notificationUID, $schoolUID) {
		global $db;		

		$sql = "select * from Users_User where school='" . $db->addMarkup($schoolUID) . "'";
		$result = $db->query($sql);

		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$this->addUser($notificationUID, $row['UID']);
		}
	}


	//----------------------------------------------------------------------------------------------
	//.	add all admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Schools_School object [string]
	//arg: grade - a school grade [string]

	function addSchoolGrade($notificationUID, $schoolUID, $grade) {
		global $db;

		$sql = "select * from Users_User"
			 . " where school='" . $db->addMarkup($schoolUID) . "'"
			 . " and grade='" . $db->addMarkup($grade) . "'";

		$result = $db->query($sql);

		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$this->addUser($notificationUID, $row['UID']);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	add all members of a group to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Groups_Group object [string]
	
	function addGroup($notificationUID, $groupUID) {
		global $db;		

		$sql = "select * from Groups_Membership where groupUID='" . $db->addMarkup($groupUID) . "'";
		$result = $db->query($sql);

		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$this->addUser($notificationUID, $row['userUID']);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	add all friends of a given user
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: userUID - UID of a Users_User object [string]

	function addFriends($notificationUID, $userUID) {
		global $db;		

		$sql = "select * from Users_Friendship"
			 . " where userUID='" . $db->addMarkup($userUID) . "'"
			 . " and status='confirmed'";

		$result = $db->query($sql);

		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$this->addUser($notificationUID, $row['friendUID']);
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add all confirmed members and admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: projectUID - UID of a Projects_Project object [string]

	function addProject($notificationUID, $projectUID) {
		global $db;		

		//$sql = "select * from Projects_Membership"
		//	 . " where projectUID='" . $db->addMarkup($projectUID) . "'"
		//	 . " and (role='admin' OR role='member')";

		$conditions = array();
		$conditions[] = "projectUID='" . $db->addMarkup($projectUID) . "'";
		$conditions[] = "(role='admin' OR role='member')";

		$range = $db->loadRange('Projects_Membership', '*', $conditions);
		foreach ($range as $row) { $this->addUser($notificationUID, $row['userUID']); }		
	}

	//----------------------------------------------------------------------------------------------
	//.	add all admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: projectUID - UID of a Projects_Project object [string]

	function addProjectAdmins($notificationUID, $projectUID) {
		global $db;		

		// $sql = "select * from Projects_Membership"
		//	 . " where UID='" . $db->addMarkup($projectUID) . "'"
		//	 . " and role='admin'";

		$conditions = array();
		$conditions[] = "projectUID='" . $db->addMarkup($projectUID) . "'";
		$conditions[] = "role='admin'";

		$range = $db->loadRange('Projects_Membership', '*', $conditions);
		foreach ($range as $row) { $this->addUser($notificationUID, $row['userUID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add everyone participating in a forum thread
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: threadUID - UID of a Forums_Thread object [string]

	function addForumThread($notificationUID, $projectUID) {
		global $db;		
		//TODO
	}

}

?>
