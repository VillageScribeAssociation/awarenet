<?

//--------------------------------------------------------------------------------------------------
//*	object representing schools
//--------------------------------------------------------------------------------------------------

class Schools_School {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ title [string]
	var $description;		//_ wyswyg [string]
	var $geocode;			//_ varchar(255) [string]
	var $region;			//_ varchar(255) [string]
	var $country;			//_ varchar(255) [string]
	var $type;				//_ varchar(255) [string]
	var $hidden;			//_ controls whether this school shows up in lists varchar(3) [string]
	var $lastBump;			//_	records when grades were last rolled around to a new year [string]
	var $notifyAll;			//_	controls whether announcements are sent to all users or not [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor	ALTER TABLE Schools_School CHANGE `show` `hidden` VARCHAR(3)
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a School object [string]

	function Schools_School($raUID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->name = 'New School ' . $this->UID;		// set default name
			$this->hidden = 'yes';							// do not show by default
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a School object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $kapenta;
		$objary = $kapenta->db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load School object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->description = $ary['description'];
		$this->geocode = $ary['geocode'];
		$this->region = $ary['region'];
		$this->country = $ary['country'];
		$this->type = $ary['type'];
		$this->hidden = $ary['hidden'];
		$this->lastBump = $ary['lastBump'];
		$this->notifyAll = $ary['notifyAll'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('schools', 'schools_school', $this->UID, $this->name);
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
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
		if ('' == $this->hidden) { $this->hidden = 'no'; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'schools';
		$dbSchema['model'] = 'schools_school';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'name' => 'VARCHAR(255)',
			'description' => 'MEDIUMTEXT',
			'geocode' => 'VARCHAR(255)',
			'region' => 'VARCHAR(255)',
			'country' => 'VARCHAR(255)',
			'type' => 'VARCHAR(255)',
			'hidden' => 'VARCHAR(3)',
			'lastBump' => 'DATETIME',
			'notifyAll' => 'VARCHAR(10)',
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
			'name' => $this->name,
			'description' => $this->description,
			'geocode' => $this->geocode,
			'region' => $this->region,
			'country' => $this->country,
			'type' => $this->type,
			'hidden' => $this->hidden,
			'lastBump' => $this->lastBump,
			'notifyAll' => $this->notifyAll,
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
		global $kapenta;
		global $theme;

		$ary = $this->toArray();

		$ary['editUrl'] = '';		$ary['editLink'] = '';
		$ary['viewUrl'] = '';		$ary['viewLink'] = '';
		$ary['delUrl'] = '';		$ary['delLink'] = '';
		$ary['newUrl'] = '';		$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $kapenta->user->authHas('schools', 'schools_school', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%schools/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $kapenta->user->authHas('schools', 'schools_school', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%schools/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $kapenta->user->authHas('schools', 'schools_school', 'edit', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%schools/confirmdelete/UID_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $kapenta->user->authHas('schools', 'schools_school', 'new', $this->UID)) { 
			$ary['newUrl'] = "%%serverPath%%schools/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new school]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	title / type
		//------------------------------------------------------------------------------------------
		//see: http://code.google.com/p/awarenet/issues/detail?id=101 #29

		$ary['titleType'] = $ary['name'];
		if (false == strpos(strtolower($ary['type']), 'school')) {
			$ary['titleType'] = $ary['name']. ' (' . $ary['type'] . ')';
		}

		//------------------------------------------------------------------------------------------
		//	location
		//------------------------------------------------------------------------------------------

		$ary['location'] = 'unknown';
		if ('' != trim($ary['country'])) { $ary['location'] = $ary['country']; }
		if ('' != trim($ary['region'])) { 
			$ary['location'] = $ary['region'] . ', ' . $ary['location']; 
		}

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------
		//$ary['contentHtml'] = str_replace(">\n", ">", trim($ary['description']));
		//$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
		$ary['summary'] = $theme->makeSummary($ary['description']);
	
		return $ary;
	}

	
	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;

		//TODO: check that there are no students at this school before deleting

		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

}

?>
