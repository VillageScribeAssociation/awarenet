<?

//--------------------------------------------------------------------------------------------------
//*	Relates groups to the schools  its members attend.
//--------------------------------------------------------------------------------------------------

class Groups_SchoolIndex {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $groupUID;			//_ uid [string]
	var $schoolUID;			//_ title [string]
	var $memberCount;		//_ number of members at this school [int]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a SchoolIndex object [string]

	function Groups_SchoolIndex($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a SchoolIndex object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load SchoolIndex object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->groupUID = $ary['groupUID'];
		$this->schoolUID = $ary['schoolUID'];
		$this->memberCount = (int)$ary['memberCount'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $kapenta;
		$report = '';					//%	return value [string]

		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }

		if (false == $kapenta->db->objectExists('schools_School', $this->schoolUID)) {
			$report .= "Group registered at unknown school (" . $this->schoolUID . ").<br/>\n";
		}

		if (false == $kapenta->db->objectExists('groups_group', $this->groupUID)) {
			$report .= "Could not register unkown group at school: " . $this->groupUID . ".<br/>\n";
		}

		$matchUID = $this->getIndexUID($this->groupUID, $this->schoolUID);
		if (('' != $matchUID) && ($matchUID != $this->UID)) {
			$report .= ''
			 . "Association $matchUID already exists for this group "
			 . "(" . $this->groupUID . ") and school (" . $this->schoolUID . ").<br/>\n";
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'groups';
		$dbSchema['model'] = 'groups_schoolindex';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'groupUID' => 'VARCHAR(30)',
			'schoolUID' => 'VARCHAR(255)',
			'memberCount' => 'BIGINT(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'groupUID',
			'schoolUID'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'groupUID' => $this->groupUID,
			'schoolUID' => $this->schoolUID,
			'memberCount' => (string)$this->memberCount,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
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
		if (true == $user->authHas('groups', 'groups_schoolindex', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%groups/showschoolindex/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('groups', 'groups_schoolindex', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%groups/editschoolindex/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('groups', 'groups_schoolindex', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%groups/delschoolindex/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get UID of Groups_SchoolIndex object if available
	//----------------------------------------------------------------------------------------------
	//arg: groupUID - UID of a Groups_Group object [string]
	//arg: schoolUID - UID of a Schools_School object [string]
	//returns: UID if found, null string

	function getIndexUID($groupUID, $schoolUID) {
		global $kapenta;
		$uid = '';			//%	return value [string]

		$conditions = array();
		$conditions[] = "groupUID='" . $kapenta->db->addMarkup($groupUID) . "'";
		$conditions[] = "schoolUID='" . $kapenta->db->addMarkup($schoolUID) . "'";

		$range = $kapenta->db->loadRange('groups_schoolindex', '*', $conditions);

		foreach ($range as $item) { return $item['UID']; }
		return $uid;
	}

}

?>
