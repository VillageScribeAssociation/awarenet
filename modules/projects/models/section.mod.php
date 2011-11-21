<?

//--------------------------------------------------------------------------------------------------
//*	An individual section of a project.
//--------------------------------------------------------------------------------------------------

class Projects_Section {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $projectUID;		//_ ref:Projects_Project [string]
	var $parentUID;			//_ ref:Projects_Section [string]
	var $title;				//_ title [string]
	var $content;			//_ wyswyg [string]
	var $weight;			//_ bigint [string]
	var $hidden;			//_ (yes|no) [string]
	var $lockedOn;			//_	datetime [string]
	var $lockedBy;			//_ ref:Users_User [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]

	var $lockTimeout = 600;	//_	ten minutes, TODO: make registry setting [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Section object [string]

	function Projects_Section($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();			// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {					// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->hidden = 'no';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Section object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Section object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->projectUID = $ary['projectUID'];
		$this->parentUID = $ary['parentUID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->weight = $ary['weight'];
		$this->hidden = $ary['hidden'];
		$this->lockedOn = $ary['lockedOn'];
		$this->lockedBy = $ary['lockedBy'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];

		$this->checkLock();							// clear any expired lock on this section

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'projects';
		$dbSchema['model'] = 'projects_section';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'projectUID' => 'VARCHAR(30)',
			'parentUID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'weight' => 'BIGINT(20)',
			'lockedOn' => 'DATETIME',
			'hidden' => 'VARCHAR(10)',
			'lockedBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'parentUID' => '10',
			'lockedOn' => '',
			'lockedBy' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'projectUID',
			'parentUID',
			'title',
			'content',
			'weight'
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
			'projectUID' => $this->projectUID,
			'parentUID' => $this->parentUID,
			'title' => $this->title,
			'content' => $this->content,
			'weight' => $this->weight,
			'hidden' => $this->hidden,
			'lockedOn' => $this->lockedOn,
			'lockedBy' => $this->lockedBy,
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
		$ext['titleLink'] = '';

		$ext['editInlineLink'] = '';
		$ext['delInlineLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('projects', 'projects_section', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%projects/show/'. $ext['projectUID'] .'#s'. $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
			$ext['titleLink'] = "<a href='" . $ext['viewUrl'] . "'>" . $ext['title'] . "</a>";
		}

		if (true == $user->authHas('projects', 'projects_section', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%projects/editsection/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";

			$onClick = "project.editSection('" . $this->UID . "');";

			$ext['editInlineLink'] = "<a href='#s". $this->UID ."' onClick=\"$onClick\">[edit]</a>";
		}

		if (true == $user->authHas('projects', 'projects_section', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%projects/delsection/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";

			$titleJs = str_replace("'", '`', $this->title);
			$onClick = "project.deleteSection('" . $this->UID . "', '" . $titleJs . "');";

			$ext['delInlineLink'] = "<a href='#s". $this->UID ."' onClick=\"$onClick\">[delete]</a>";
		}

		$ext['ordinal'] = ((int)$ext['weight'] + 1) . '';

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
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
	//	LOCK
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	check if locked and clear any expired lock
	//----------------------------------------------------------------------------------------------
	//returns: empty string if not locked, or UID of user who owns lock [string]

	function checkLock() {
		global $kapenta;
		if ('' != $this->lockedBy) {
			$expires = $kapenta->strtotime($this->lockedOn) + $this->lockTimeout;	//% [int]
			$currTime = $kapenta->time();											//% [int]
			if ($expires < $currTime) {
				$this->lockedBy = '';
				$this->lockedOn = $kapenta->datetime();
				$this->save();
			}
		}

		return $this->lockedBy;
	}

	//----------------------------------------------------------------------------------------------
	//.	(re)set lock - limit editing of this item to a single user
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [string]

	function setLock($userUID) {
		global $kapenta;
		$lock = $this->checkLock();									//%	ref:Users_User [string]

		if (('' != $lock) && ($userUID != $lock)) { return false; }	// someone else owns it

		$this->lockedOn = $kapenta->datetime();
		$this->lockedBy = $userUID;
		$report = $this->save();
		if ('' == $report) { return true; }
		return false;
	}

}

?>
