<?

//--------------------------------------------------------------------------------------------------
//*	A description of an event which users may view in their feed
//--------------------------------------------------------------------------------------------------

class Notifications_Notification {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;					//_	currently loaded database record [array]
	var $dbSchema;				//_	database table definition [array]
	var $loaded = false;		//_	set to true when an object has been loaded [bool]

	var $UID;					//_ UID [string]
	var $refModule;				//_ module [string]
	var $refModel;				//_ model [string]
	var $refUID;				//_ ref:*_* [string]
	var $title;					//_ title [string]
	var $content;				//_ wyswyg [string]
	var $refUrl;				//_ varchar(255) [string]
	var $createdOn;				//_ datetime [string]
	var $createdBy;				//_ ref:Users_User [string]
	var $editedOn;				//_ datetime [string]
	var $editedBy;				//_ ref:Users_User [string]

	var $members;				//_	people to whom this notification was sent [array]
	var $membersLoaded = false;	//_	set to true when members are loaded

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Notification object [string]

	function Notifications_Notification($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Notification ' . $this->UID;		// set default title
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Notification object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Notification object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->refUrl = $ary['refUrl'];
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
		$dbSchema['model'] = 'Notifications_Notification';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'TEXT',
			'refModel' => 'TEXT',
			'refUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'content' => 'MEDIUMTEXT',
			'refUrl' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'title' => $this->title,
			'content' => $this->content,
			'refUrl' => $this->refUrl,
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

		$xml = $indent . "<kobject type='Notifications_Notification'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <content><![CDATA[" . $this->content . "]]></content>\n"
			. $indent . "    <refUrl>" . $this->refUrl . "</refUrl>\n"
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
	global $theme, $utils, $user;
		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('notifications', 'Notifications_Notification', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Notifications/shownotification/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('notifications', 'Notifications_Notification', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Notifications/editnotification/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('notifications', 'Notifications_Notification', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Notifications/delnotification/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	general
		//------------------------------------------------------------------------------------------

		$ext['createdOnLong'] = date('F nS Y', strtotime($ext['createdOn']));
		$ext['editedOnLong'] = date('F nS Y', strtotime($ext['editedOn']));		

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		$ext['contentJsVar64'] = 'content' . $utils->makeAlphaNumeric($ext['UID']) . 'Js64';
		$ext['contentJs64'] = $utils->base64EncodeJs($ext['contentJsVar64'], $ext['content']);
		$ext['contentSummary'] = $theme->makeSummary($ext['content']);
		return $ext;
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

	//==============================================================================================
	//	memberships (people to whom the notification is addressed)
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//. load membership of this notification
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function loadMembers() {
		global $db;
		$conditions = array("notificationUID='" . $db->addMarkup($this->UID) . "'");
		$range = $db->loadRange('Notifications_UserIndex', '*', $conditions, '');
		if (false === $range) { return false; }
		$this->members = $range;
		$this->membersLoaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. discover if a user is amember of this 
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function hasMember($userUID) {
		if (false == $this->membersLoaded) { $this->loadMembers(); }
		foreach($this->members as $membership) 
			{ if ($membership['userUID'] == $userUID) { return true; } }
		return false;
	}

}

?>
