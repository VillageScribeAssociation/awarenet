<?

//--------------------------------------------------------------------------------------------------
//*	relationship object for group membership (Users_User <-> Groups_Group)
//--------------------------------------------------------------------------------------------------

class Groups_Membership {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $userUID;			//_ ref:Users_User [string]
	var $groupUID;			//_ ref:Groups_Group [string]
	var $position;			//_ position within group [string]
	var $admin;				//_ is group admin? (yes|no) [string]
	var $joined;			//_ datetime [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Membership object [string]

	function Groups_Membership($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Membership object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load('Groups_Membership', $UID);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load (first) object from the db given user and group UIDs
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//arg: groupUID - UID of a Groups_Group object [string]
	//returns: true on success, false on failure [bool]

	function loadMembership($userUID, $groupUID) {
		global $db;

		$conditions = array();
		$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";
		$conditions[] = "groupUID='" . $db->addMarkup($groupUID) . "'";
		$range = $db->loadRange('Groups_Membership', '*', $conditions);

		foreach($range as $row) {
			$this->loadArray($row);
			return true;
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Membership object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->userUID = $ary['userUID'];
		$this->groupUID = $ary['groupUID'];
		$this->position = $ary['position'];
		$this->admin = $ary['admin'];
		$this->joined = $ary['joined'];
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
	//.	verify - check that object is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $db;
		$report = '';

		if (strlen($this->UID) < 5) 
			{ $report .= "UID not present.\n"; }
		if (false == $db->objectExists('Users_User', $this->userUID)) 
			{ $report .= "Member does not exist."; }
		if (false == $db->objectExists('Groups_Group', $this->groupUID)) 
			{ $report .= "Group does not exist."; }

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'groups';
		$dbSchema['table'] = 'Groups_Membership';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'userUID' => 'VARCHAR(33)',
			'groupUID' => 'VARCHAR(33)',
			'position' => 'VARCHAR(30)',
			'admin' => 'VARCHAR(10)',
			'joined' => 'DATETIME',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'userUID' => '10',
			'groupUID' => '10',
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
			'userUID' => $this->userUID,
			'groupUID' => $this->groupUID,
			'position' => $this->position,
			'admin' => $this->admin,
			'joined' => $this->joined,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete('groups', 'Groups_Membership', $this->UID)) { return false; }
		return true;
	}

}

?>
