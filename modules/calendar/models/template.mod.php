<?

//--------------------------------------------------------------------------------------------------
//*	Templates for running off multiple calendar entries.
//--------------------------------------------------------------------------------------------------

class Calendar_Template {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $category;			//_ varchar(100) [string]
	var $venue;				//_ varchar(255) [string]
	var $content;			//_ wyswyg [string]
	var $year;				//_ varchar(10) [string]
	var $month;				//_ varchar(10) [string]
	var $day;				//_ varchar(10) [string]
	var $eventStart;		//_ varchar(50) [string]
	var $eventEnd;			//_ varchar(50) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Template object [string]

	function Calendar_Template($raUID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		    // initialise table schema
		if ('' != $raUID) { $this->load($raUID); }	    // try load an object from the database
		if (false == $this->loaded) {			                        // check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Template object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Template object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->category = $ary['category'];
		$this->venue = $ary['venue'];
		$this->content = $ary['content'];
		$this->year = $ary['year'];
		$this->month = $ary['month'];
		$this->day = $ary['day'];
		$this->eventStart = $ary['eventStart'];
		$this->eventEnd = $ary['eventEnd'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('calendar', 'calendar_template', $this->UID, $this->title);
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
		$dbSchema['module'] = 'calendar';
		$dbSchema['model'] = 'calendar_template';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'category' => 'VARCHAR(100)',
			'venue' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'year' => 'VARCHAR(10)',
			'month' => 'VARCHAR(10)',
			'day' => 'VARCHAR(10)',
			'eventStart' => 'VARCHAR(50)',
			'eventEnd' => 'VARCHAR(50)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'alias' => 'VARCHAR(255)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'category' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'title',
			'category',
			'venue',
			'content',
			'year',
			'month',
			'day',
			'eventStart',
			'eventEnd'
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
			'title' => $this->title,
			'category' => $this->category,
			'venue' => $this->venue,
			'content' => $this->content,
			'year' => $this->year,
			'month' => $this->month,
			'day' => $this->day,
			'eventStart' => $this->eventStart,
			'eventEnd' => $this->eventEnd,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
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
		$ext['nameLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('calendar', 'calendar_template', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%calendar/showtemplate/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('calendar', 'calendar_template', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%calendar/edittemplate/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
			$ext['nameLink'] = "<a href='" . $ext['editUrl'] . "'>" . $ext['title'] . "</a>";
		}

		if (true == $user->authHas('calendar', 'calendar_template', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%calendar/deltemplate/' . $ext['alias'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

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

}

?>
