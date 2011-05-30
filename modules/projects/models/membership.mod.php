<?

//--------------------------------------------------------------------------------------------------
//*	index table (users <-> projects)
//--------------------------------------------------------------------------------------------------
//+	project members can all edit the project, but only the person who intiated the project may 
//+	add members.
//+
//+	Member role may be:
//+	- admin (can add members, edit project)
//+	- member (can edit project)

class Projects_Membership {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $projectUID;		//_ varchar(33) [string]
	var $userUID;			//_ varchar(33) [string]
	var $role;				//_ varchar(10) [string]
	var $joined;			//_ datetime [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Membership object [string]

	function Projects_Membership($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
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
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record given project and user UIDs
	//----------------------------------------------------------------------------------------------
	//arg: projectUID - UID of a project [string]
	//arg: userUID - UID of a user [string]	
	//returns: true if record is found, false if not found [bool]

	function findAndLoad($projectUID, $userUID) {
		global $db;

		//	$sql = "select * from Projects_Membership "
		//	 . "where projectUID='" . $db->addMarkup($projectUID) . "' "
		//	 . "and userUID='" . $db->addMarkup($userUID) . "'";		

		$conditions = array();
		$conditions[] = "projectUID='" . $db->addMarkup($projectUID) . "'";
		$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";
		$range = $db->loadRange('projects_membership', '*', $conditions);

		if (count($range) == 0) { return false; }
		
		foreach($range as $row) {
			$this->loadArray($row);
			return true;
		}
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
		$this->projectUID = $ary['projectUID'];
		$this->userUID = $ary['userUID'];
		$this->role = $ary['role'];
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
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $db;
		$report = '';

		if (strlen($this->UID) < 5) 
			{ $report .= "UID not present.\n"; }
		if (false == $db->objectExists('users_user', $this->userUID)) 
			{ $report .= "Member does not exist."; }
		if (false == $db->objectExists('projects_project', $this->projectUID)) 
			{ $report .= "Project does not exist."; }

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'projects';
		$dbSchema['model'] = 'projects_membership';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'projectUID' => 'VARCHAR(33)',
			'userUID' => 'VARCHAR(33)',
			'role' => 'VARCHAR(10)',
			'joined' => 'DATETIME',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'projectUID' => '10',
			'userUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'projectUID',
			'userUID',
			'role',
			'joined' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'projectUID' => $this->projectUID,
			'userUID' => $this->userUID,
			'role' => $this->role,
			'joined' => $this->joined,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('projects', 'projects_membership', 'show', $this->UID)) {
			$ext['viewUrl'] = "TODO";
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('projects', 'projects_membership', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Projects/editmembership/' . $this->UID;
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('projects', 'projects_membership', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Projects/delmembership/' . $this->UID;
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

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

}

?>
