<?

	require_once(dirname(__FILE__) . '/../modules/notifications/models/notification.mod.php');
	require_once(dirname(__FILE__) . '/../modules/notifications/models/userindex.mod.php');

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
	//arg: refModule - name of kapenta module to which notification pertains [string]
	//arg: refModel - type of object this notification is about [string]
	//arg: refUID - UID of object this notification is about [string]
	//arg: refEvent - type of event this notification is about [string]
	//arg: title - title of notification (plaintext) [string]
	//arg: content - body of notification (html) [string]
	//opt: url - link to subject of notification [string]
	//opt: private - not to be shared in global or category feeds [string]
	//returns: UID of new notification, or empty string on failure [string]

	function create($refModule, $refModel, $refUID, $refEvent, $title, $content, $url = '', $private = false) {
		global $kapenta;
		global $user;

		$model = new Notifications_Notification();

		$model->refModule = $refModule;
		$model->refModel = $refModel;
		$model->refUID = $refUID;
		$model->refEvent = $refEvent;
		$model->title = $title;
		$model->content = $content;
		$model->refUrl = str_replace($kapenta->serverPath, '%%serverPath%%', $url);
		$report = $model->save();

		if ('' == $report) {
			if (false == $private) { 
				$this->addUser($model->UID, 'everyone');
				if ('teacher' == $user->role) { $this->addUser($model->UID, 'teachers'); }
			}
			return $model->UID;
		}

		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	count notifications made about some object
	//----------------------------------------------------------------------------------------------
	//arg: refModule - name of a mapenta module [string]
	//arg: refModel - type of object notifications are about [string]
	//arg: refUID - UID of object notifications are about [string]
	//returns: number of notifications about this object [string]

	function count($refModule, $refModel, $refUID) {
		global $kapenta;
		$conditions = array();
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";
		$count = $kapenta->db->countRange('notifications_notification', $conditions);
		return $count;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a notification has been made about the same thing recently
	//----------------------------------------------------------------------------------------------
	//arg: refModule - name of a mapenta module [string]
	//arg: refModel - type of object notifications are about [string]
	//arg: refUID - UID of object notifications are about [string]
	//arg: refEvent - event which caused notification to be sent, '*' for all [string]
	//arg: userUID - UID of user who caused this notification to be sent, '*' for all [string]
	//arg: maxAge - maximum age of a event, in seconds [string]
	//returns: UID of recent notification, if it exists, null string if not [string]

	function existsRecent($refModule, $refModel, $refUID, $userUID, $refEvent, $maxAge) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	load most recent notification abot this object, event (optional) and user (optional)
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
		$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
		$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

		if (('*' != $refEvent) && ('' != $refEvent)) {
			$conditions[] = "refEvent='" . $kapenta->db->addMarkup($refEvent) . "'";
		}

		if (('*' != $userUID) && ('' != $userUID)) {
			$conditions[] = "createdBy='" . $kapenta->db->addMarkup($userUID) . "'";
		} 

		$by = "createdOn DESC";
		$range = $kapenta->db->loadRange('notifications_notification', '*', $conditions, $by, '10');

		//------------------------------------------------------------------------------------------
		//	if such a notification exists, see if it is younger than maxAge
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			$lastEdit = $kapenta->strtotime($item['editedOn']);
			$oldest = $kapenta->time() - $maxAge;
			if ($lastEdit > $oldest) { return $item['UID'];  }	// found one
		}	

		return '';												// no event younger than maxAge
	}	

	//----------------------------------------------------------------------------------------------
	//.	add further content to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function annotate($notificationUID, $annotation) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	update the notification
		//------------------------------------------------------------------------------------------
		$model = new Notifications_Notification($notificationUID);	// load the notification
		if (false == $model->loaded) { return false; }				// no such notification
		$model->content .= $annotation;
		$report = $model->save();
		if ('' != $report) { return false; }						// could not save

		//------------------------------------------------------------------------------------------
		//	bump to the top of users feeds
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "notificationUID='" . $kapenta->db->addMarkup($model->UID) . "'";
		$range = $kapenta->db->loadRange('notifications_userindex', '*', $conditions);

		foreach($range as $item) {
			$ui = new Notifications_UserIndex($item['UID']);
			$ui->save();
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	add a user to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function addUser($notificationUID, $userUID) {
		$model = new Notifications_UserIndex();
		if (true == $model->exists($notificationUID, $userUID)) { return true; }	// already added
		$model->notificationUID = $notificationUID;
		$model->userUID = $userUID;
		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add all users to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//returns: true on success, false on failure [bool]

	function addEveryone($notificationUID) {
		global $kapenta;
		$allOk = true;

		$sql = "select UID, role from users_user";
		$result = $kapenta->db->query($sql);

		while($row = $kapenta->db->fetchAssoc($result)) {
			$row = $kapenta->db->rmArray($row);
			if (('public' != $row['role']) && ('banned' != $row['role'])) {
				$check = $this->addUser($notificationUID, $kapenta->db->removeMarkup($row['UID']));
				if (false == $check) { $allOk = false; }
			}
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	add all admin users and teachers to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//returns: true on success, false on failure [bool]

	function addAdmins($notificationUID) {
		global $kapenta;
		$allOk = true;		//%	return value [bool]

		$range = $kapenta->db->loadRange('users_user', '*', array("role='admin' OR role ='teacher'"));
		foreach($range as $row) {
			if (false == $this->addUser($notificationUID, $row['UID'])) { $allOk = false; }
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	add an entire school to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Schools_School object [string]
	
	function addSchool($notificationUID, $schoolUID) {
		global $kapenta;		
		$conditions = array("school='" . $kapenta->db->addMarkup($schoolUID) . "'");
		$range = $kapenta->db->loadRange('users_user', '*', $conditions);
		foreach ($range as $item) { $this->addUser($notificationUID, $item['UID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add all admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Schools_School object [string]
	//arg: grade - a school grade [string]

	function addSchoolGrade($notificationUID, $schoolUID, $grade) {
		global $kapenta;

		$conditions = array();
		$conditions[] = "school='" . $kapenta->db->addMarkup($schoolUID) . "'";
		$conditions[] = "grade='" . $kapenta->db->addMarkup($grade) . "'";

		$range = $kapenta->db->loadRange('users_user', '*', $conditions);
		foreach ($range as $item) { $this->addUser($notificationUID, $item['UID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add all members of a group to a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: schoolUID - UID of a Groups_Group object [string]
	
	function addGroup($notificationUID, $groupUID) {
		global $kapenta;		

		$conditions = array();
		$conditions[] = "groupUID='" . $kapenta->db->addMarkup($groupUID) . "'";

		$range = $kapenta->db->loadRange('groups_membership', '*', $conditions);
		foreach ($range as $item) { $this->addUser($notificationUID, $item['userUID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add all friends of a given user
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: userUID - UID of a Users_User object [string]

	function addFriends($notificationUID, $userUID) {
		global $kapenta;		

		$conditions = array();
		$conditions[] = "userUID='" . $kapenta->db->addMarkup($userUID) . "'";
		$conditions[] = "status='confirmed'";

		$range = $kapenta->db->loadRange('users_friendship', '*', $conditions);

		foreach ($range as $item) { $this->addUser($notificationUID, $item['friendUID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add all confirmed members and admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: projectUID - UID of a Projects_Project object [string]

	function addProject($notificationUID, $projectUID) {
		global $kapenta;		

		//$sql = "select * from Projects_Membership"
		//	 . " where projectUID='" . $kapenta->db->addMarkup($projectUID) . "'"
		//	 . " and (role='admin' OR role='member')";

		$conditions = array();
		$conditions[] = "projectUID='" . $kapenta->db->addMarkup($projectUID) . "'";
		$conditions[] = "(role='admin' OR role='member')";

		$range = $kapenta->db->loadRange('projects_membership', '*', $conditions);
		foreach ($range as $row) { $this->addUser($notificationUID, $row['userUID']); }		
	}

	//----------------------------------------------------------------------------------------------
	//.	add all admins of a project
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: projectUID - UID of a Projects_Project object [string]

	function addProjectAdmins($notificationUID, $projectUID) {
		global $kapenta;		

		// $sql = "select * from Projects_Membership"
		//	 . " where UID='" . $kapenta->db->addMarkup($projectUID) . "'"
		//	 . " and role='admin'";

		$conditions = array();
		$conditions[] = "projectUID='" . $kapenta->db->addMarkup($projectUID) . "'";
		$conditions[] = "role='admin'";

		$range = $kapenta->db->loadRange('projects_membership', '*', $conditions);
		foreach ($range as $row) { $this->addUser($notificationUID, $row['userUID']); }
	}

	//----------------------------------------------------------------------------------------------
	//.	add everyone participating in a forum thread
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: threadUID - UID of a Forums_Thread object [string]

	function addForumThread($notificationUID, $projectUID) {
		global $kapenta;		
		//TODO
	}

	//----------------------------------------------------------------------------------------------
	//.	get the content of a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//returns: Body of notification on success, empty string on failure [string]

	function getContent($notificationUID) {
		$model = new Notifications_Notification($notificationUID);
		if (false == $model->loaded) { return ''; }
		return $model->content;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the content of a notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: content - new body of notification [string]
	//returns: true on success, false on failure [string]

	function setContent($notificationUID, $content) {
		$model = new Notifications_Notification($notificationUID);
		if (false == $model->loaded) { return false; }
		$model->content = $content;
		$model->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the title of an existing notification
	//----------------------------------------------------------------------------------------------
	//arg: notificationUID - UID of a Notifications_Notification object [string]
	//arg: title - new title of notification [string]
	//returns: true on success, false on failure [string]

	function setTitle($notificationUID, $title) {
		$model = new Notifications_Notification($notificationUID);
		if (false == $model->loaded) { return false; }
		$model->title = $title;
		$model->save();
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get database schema for notifications_notification object
	//----------------------------------------------------------------------------------------------
	//returns: database schema of notifications_notification objects [array]

	function getDbSchema() {
		$model = new Notifications_Notification();
		return $model->getDbSchema();
	}

}

?>
