<?

//--------------------------------------------------------------------------------------------------
//*	These link notifications to users and are aggregated to form the feed
//--------------------------------------------------------------------------------------------------

class Notifications_UserIndex {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $userUID;			//_ ref:Users_User or category [string]
	var $notificationUID;	//_ ref:Notifications_Notification [string]
	var $status;			//_	notification status/visibility (show|hide) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a UserIndex object [string]

	function Notifications_UserIndex($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->status = 'show';							// visible in feed by default
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a UserIndex object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load UserIndex object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->userUID = $ary['userUID'];
		$this->notificationUID = $ary['notificationUID'];
		$this->status = $ary['status'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'notifications';
		$dbSchema['model'] = 'notifications_userindex';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'userUID' => 'VARCHAR(33)',
			'notificationUID' => 'VARCHAR(33)',
			'status' => 'VARCHAR(20)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'userUID' => '10',
			'notificationUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will not be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'userUID',
			'notificationUID',
			'status',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'userUID' => $this->userUID,
			'notificationUID' => $this->notificationUID,
			'status' => $this->status,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='notifications_userindex'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <userUID>" . $this->userUID . "</userUID>\n"
			. $indent . "    <notificationUID>" . $this->notificationUID . "</notificationUID>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $utils, $user;

		$ext = $this->toArray();		//% extended array of properties [array:string]
		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		$objType = 'notifications_userindex';

		if (true == $user->authHas('notifications', $objType, 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Notifications/showuserindex/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('notifications', $objType, 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Notifications/edituserindex/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('notifications', $objType, 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Notifications/deluserindex/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['hideJsLink'] = '';
		if (('admin' == $user->role) || ($user->UID == $ext['userUID'])) { 
			$ext['hideJsLink'] = ''
		 	. "<a href='javascript:void(0);' "
				. "onClick=\"notifications_hide('" . $ext['UID'] . "')\""
				. ">[hide]</a>";		// users can hide their own notifications
		}
		
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. perform maintenance tasks on this object
	//----------------------------------------------------------------------------------------------
	//returns: array of HTML notes on any action taken [array:string:html]

	function maintain() {
		global $db;
		$notes = array();
		
		//------------------------------------------------------------------------------------------
		//	check references to other objects
		//------------------------------------------------------------------------------------------
		if (false == $db->objectExists('users_user', $this->userUID)) {
			// TODO: take action here, delete?
			$notes[] = 'object ' . $this->UID . ' has invalid reference to user ' . $this->userUID
					 . 'in field userUID<!-- error -->';
		}

		if (false == $db->objectExists('notifications_notification', $this->notificationUID)) {
			// TODO: take action here, delete?
			$notes[] = 'object ' . $this->UID . ' has invalid reference to notification ' 
					 . $this->notificationUID . 'in field notificationUID<!-- error -->';
		}

		if (false == $db->objectExists('users_user', $this->createdBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$notes[] = 'object ' . $this->UID . ' has invalid reference to user ' . $this->userUID
					 . 'in field createdBy<!-- error -->';
		}

		if (false == $db->objectExists('users_user', $this->editedBy)) {
			// TODO: take action here, if possibe assign valid reference to a Users_User
			$notes[] = 'object ' . $this->UID . ' has invalid reference to user ' . $this->userUID
					 . 'in field editedBy<!-- error -->';
		}

		return $notes;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. discover if a userindex exists
	//----------------------------------------------------------------------------------------------
	//returns: true on if exists, false if not [bool]

	function exists($notificationUID, $userUID) {
		global $db;

		$conditions = array();
		$conditions[] = "notificationUID='" . $db->addMarkup($notificationUID) . "'";	
		$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";	

		$num = $db->countRange('notifications_userindex', $conditions);
		if ($num > 0) { return true; }
		return false;
	}

}

?>
