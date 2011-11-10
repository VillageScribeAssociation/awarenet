<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/schoolindex.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object representing user groups
//--------------------------------------------------------------------------------------------------
//+	group type could be Team/Club/Society, etc

class Groups_Group {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;					//_	currently loaded database record [array]
	var $dbSchema;				//_	database table definition [array]
	var $loaded;				//_	set to true when an object has been loaded [bool]

	var $UID;					//_ UID [string]
	var $school;				//_ varchar(33) [string]
	var $name;					//_ title [string]
	var $type;					//_ varchar(30) [string]
	var $description;			//_ wyswyg [string]
	var $createdOn;				//_ datetime [string]
	var $createdBy;				//_ ref:Users_User [string]
	var $editedOn;				//_ datetime [string]
	var $editedBy;				//_ ref:Users_User [string]
	var $alias;					//_ alias [string]

	var $schools;				//_	array of serialized Groups_SchoolIndex objects [array:array]
	var $schoolsLoaded = false;	//_	set to true when schools index objects have been loaded [bool]

	var $members;				//_ array of serialized Groups_Membership objects [array]
	var $membersLoaded = false;	//_	set to true when memberships loaded [bool]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Group object [string]

	function Groups_Group($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->name = 'New Group ' . $this->UID;		// set default name
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Group object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Group object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->school = $ary['school'];
		$this->name = $ary['name'];
		$this->type = $ary['type'];
		$this->description = $ary['description'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];

		$this->membersLoaded = false;

		$this->members = $this->getMembers();
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
		$this->updateSchoolsIndex();
		$this->alias = $aliases->create('groups', 'groups_group', $this->UID, $this->name);
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
		$dbSchema['module'] = 'groups';
		$dbSchema['model'] = 'groups_group';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'school' => 'VARCHAR(33)',
			'name' => 'VARCHAR(255)',
			'type' => 'VARCHAR(30)',
			'description' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

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
			'school' => $this->school,
			'name' => $this->name,
			'type' => $this->type,
			'description' => $this->description,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user, $theme;
		$ary = $this->toArray();
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('groups', 'groups_group', 'view', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%groups/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $user->authHas('groups', 'groups_group', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%groups/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $user->authHas('groups', 'groups_group', 'edit', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%groups/confirmdelete/UID_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $user->authHas('groups', 'groups_group', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%groups/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new group]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	look up school info
		//------------------------------------------------------------------------------------------
		$mySchool = new Schools_School($this->school);

		$ary['schoolName'] = $mySchool->name;
		$ary['schoolCountry'] = $mySchool->country;
		$ary['schoolRecordAlias'] = $mySchool->alias;
		$ary['schoolUrl'] = '%%serverPath%%/schools/' . $mySchool->alias;
		$ary['schoolLink'] = "<a href='" . $ary['schoolUrl'] . "'>" . $mySchool->name . "</a>";

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------
		$ary['contentHtml'] = $ary['description'];
		//$ary['contentHtml'] = str_replace(">\n", ">", $ary['description']);
		//$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);

		$ary['summary'] = $theme->makeSummary($ary['description'], 400);

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		return $ary;
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
	//	MEMBERS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	return list of members, fetching from database if not already available
	//----------------------------------------------------------------------------------------------
	//returns: range of serialized Groups_Membership objects [array]

	function getMembers() {
		if (false == $this->membersLoaded) {
			$this->members = $this->loadMembers();
			$this->membersLoaded = true;
		}

		return $this->members;
	}

	//----------------------------------------------------------------------------------------------
	//.	load all Groups_Membership objects relating to this group from the database
	//----------------------------------------------------------------------------------------------
	//returns: range of serialized Groups_Membership objects [array]

	function loadMembers() {
		global $db;
		if (false == $db->tableExists('groups_membership')) { return array(); }

		//echo "loading members of group: " . $this->UID . "<br/>\n";

		$conditions = array();
		$conditions[] = "groupUID='" . $db->addMarkup($this->UID) . "'";
		$range = $db->loadRange('groups_membership', '*', $conditions);

		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a new member to the group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: position - position or role within the group [string]
	//arg: admin - whether the member is an admin of this group (yes|no) [string]
	//returns: true on success, false on failure [bool]

	function addMember($userUID, $position, $admin) {
		global $kapenta, $db;
		$saved = false;									//%	return value [bool]

		$this->removemember($userUID);					//	clear out any previous membership

		$model = new Groups_Membership();				//	(re)create with position and admin
		$model->UID = $kapenta->createUID();			//	privileges as called.
		$model->userUID = $userUID;
		$model->groupUID = $this->UID;
		$model->position = $position;
		$model->admin = $admin;
		$model->joined = $db->datetime();
		$report = $model->save();

		if ('' == $report) { 							//	any html report indicates db error
			$saved = true;								//	was saved to database
			$this->members = $this->loadMembers();		//	update members list
			$this->updateSchoolsIndex();				//	update associations with schools
		}

		return $saved;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a member from the group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true on success, false on failure [bool]

	function removeMember($userUID) {
		$model = new Groups_Membership();
		$model->loadMembership($userUID, $this->UID);
		if (false == $model->loaded) { return false; }
		$result = $model->delete();

		if (true == $result) {
			$this->members = $this->loadMembers();		//	update members list
			$this->updateSchoolsIndex();				//	update associations with schools
		}

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a user belongs to a group
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object to search for [string]
	//returns: true if membership is found for the given user, false if not [bool]

	function hasMember($userUID) {
		$members = $this->getMembers();
		foreach($members as $membership) {
			if ($userUID == $membership['userUID']) { return true; }
		}
		return false;		
	}

	//----------------------------------------------------------------------------------------------
	//.	determine if user can edit the group's membership /page (DEPRECATED) TODO: remove
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if use is a group admin, teacher or site admin, otherwise false [bool]

	function hasEditAuth($userUID) {
		global $session;
		//TODO: user permission set
		//$session->msgAdmin('deprecated: groups_group::authHas() => users_user::authHas()', 'bug');
		$model = new Users_User($userUID);
		if ($model->role == 'admin') { return true; }
		if ($model->role == 'teacher') { return true; }

		foreach($this->members as $row) { 
			if (($row['userUID'] == $userUID) && ('yes' == $row['admin'])) { return true; } 	
		}	

		return false;
	}

	//==============================================================================================
	//	SCHOOLS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	returns the set of Groups_SchoolIndex objects belonging to this group
	//----------------------------------------------------------------------------------------------

	function getSchoolsIndex() {
		if (false == $this->schoolsLoaded) {
			$this->schools = $this->loadSchoolsIndex();
			$this->schoolsLoaded = true;
		}
		return $this->schools;
	}

	//----------------------------------------------------------------------------------------------
	//.	loads the set of Groups_SchoolIndex objects belonging to this group
	//----------------------------------------------------------------------------------------------
	//returns: array of serialized Groups_SchoolIndex objects [array:array]

	function loadSchoolsIndex() {
		global $db;
		$conditions = array("groupUID='" . $db->addMarkup($this->UID) . "'");
		$range = $db->loadRange('groups_schoolindex', '*', $conditions);
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	discovers if a group has members at a given school
	//----------------------------------------------------------------------------------------------	
	//arg: schoolUID - UID of a Schools_School object [string]
	//returns: true if member(s) found, false if not [bool]

	function hasSchool($schoolUID) {
		$schools = $this->getSchoolsIndex();
		$found = false;							//%	return value [bool]

		foreach($this->schools as $schoolIndex) {
			if ($schoolUID == $schoolIndex['schoolUID']) { $found = true; }
		}

		return $found;
	}

	//----------------------------------------------------------------------------------------------
	//.	updates list of schools at which this group has members
	//----------------------------------------------------------------------------------------------	
	//returns: true on success, false on failure [bool]

	function updateSchoolsIndex() {
		global $theme;
		global $db;
		global $session;

		//echo "[i] updating school index of group " . $this->name . " (" . $this->UID . ")<br/>\n";

		$updated = true;								//%	return value [bool]
		$members = $this->getMembers();					//%	array of memberships [array]
		$oldSchools = $this->getSchoolsIndex();			//%	extant schools index set [array]	
		$newSchools = array();							//%	UIDs for comparison [array]

		//echo "Members: " . count($this->members) . "<br/>\n";

		//------------------------------------------------------------------------------------------
		//	for each member, discover if their school is linked to this group
		//------------------------------------------------------------------------------------------
		foreach($members as $member) {
			// look up UID of the school this member attends
			$block = "[[:users::schooluid::userUID=" . $member['userUID'] . ":]]";
			$schoolUID = $theme->expandBlocks($block, '');
	
			//echo "schoolUID: $schoolUID groupUID: " . $this->UID . "<br/>\n";

			// add to list of schools if not already present
			if (false == in_array($schoolUID, $newSchools)) { 
				//echo "added $schoolUID to list<br/>\n";
				$newSchools[] = $schoolUID; 
			}
		
			// associate school with this group if not already done
			if (false == $this->hasSchool($schoolUID)) {
				//echo "school $schoolUID not associated with group<br/>";

				//----------------------------------------------------------------------------------
				//	new association with a school
				//----------------------------------------------------------------------------------
				$model = new Groups_SchoolIndex();
				$model->groupUID = $this->UID;
				$model->schoolUID = $schoolUID;
				$report = $model->save();

				if ('' == $report) {
					$msg = "Group '" . $this->name . "' now active at "
						 . "[[:schools::name::schoolUID=" . $schoolUID . "::link=yes:]]"
						 . " (UID: $schoolUID)";
					$session->msg($msg, 'ok');
					//echo "created school index for $schoolUID<br/>";

				} else {
					$updated = false;
					$msg = "Could not add group " . $this->UID . " to school: $schoolUID <br/>" . $report;
					$session->msg($msg, 'bad');
					//echo "could not create school index for $schoolUID<br/>$report<br/>";

				}
			}
		}

		//------------------------------------------------------------------------------------------
		//	for each school this group was associated with, check it has at least one member
		//------------------------------------------------------------------------------------------
		foreach($oldSchools as $schoolIndex) {
			if (false == in_array($schoolIndex['schoolUID'], $newSchools)) {
				//----------------------------------------------------------------------------------
				//	school is associated with group but has no group members, remove
				//----------------------------------------------------------------------------------
				$model = new Groups_SchoolIndex();
				$model->loadArray($schoolIndex);
				$check = $model->delete();
				if (false == $check) { $updated = false; }
			}
		}

		$this->schools = $this->loadSchoolsIndex();			//	update list of schools
		return $updated;
	}

}

?>
