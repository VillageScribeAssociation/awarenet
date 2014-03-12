<?

//--------------------------------------------------------------------------------------------------
//*	object for storing wiki revisions
//--------------------------------------------------------------------------------------------------

class Wiki_Revision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;							//_	currently loaded database record [array]
	var $dbSchema;						//_	database table definition [array]
	var $loaded;						//_	set to true when an object has been loaded [bool]

	var $UID;							//_ UID [string]
	var $articleUID;					//_ varchar(33) [string]
	var $title;							//_ title [string]
	var $content;						//_ text [string]
	var $nav;							//_ text [string]
	var $locked;						//_ varchar(30) [string]
	var $namespace;						//_ (article|talk|template|category|etc) [string]
	var $createdOn;						//_ datetime [string]
	var $createdBy;						//_ ref:Users_User [string]
	var $editedOn;						//_ datetime [string]
	var $editedBy;						//_ ref:Users_User [string]

	var $allRevisions;					//_ nested array of UID, editedOn, editedBy, reason [array]
	var $allRevisionsLoaded = false;	//_	set to true when above is loaded [bool]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Revision object [string]

	function Wiki_Revision($UID = '') {
		global $kapenta;
		$this->allRevisions = array();
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->namespace = 'article';					// set default namespace
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Revision object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Revision object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->articleUID = $ary['articleUID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->nav = $ary['nav'];
		$this->reason = $ary['reason'];
		$this->locked = $ary['locked'];
		$this->namespace = $ary['namespace'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
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
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'wiki';
		$dbSchema['model'] = 'wiki_revision';
		$dbSchema['archive'] = 'yes';


		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'nav' => 'VARCHAR(255)',
			'locked' => 'VARCHAR(30)',
			'namespace' => 'VARCHAR(255)',
			'articleUID' => 'VARCHAR(33)',
			'reason' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will not be kept for any fields (this object *IS* the revision history)
		$dbSchema['nodiff'] = array(
			'UID',
			'title',
			'content',
			'nav',
			'locked',
			'namespace',
			'articleUID',
			'reason',
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
			'title' => $this->title,
			'content' => $this->content,
			'nav' => $this->nav,
			'locked' => $this->locked,
			'namespace' => $this->namespace,
			'articleUID' => $this->articleUID,
			'reason' => $this->reason,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $kapenta;
		global $kapenta;
		global $theme;

		$ary = $this->toArray();			//%	return value [dict]

		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if ($kapenta->user->authHas('wiki', 'wiki_revision', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/showrevision/' . $ary['UID'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[show revision &gt;&gt;]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------
		$ary['editedOnLong'] = $kapenta->longDate($ary['editedOn']);

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
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
	//.	find UID of previous version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------
	//returns: UID of previous revision, false if not found [string][bool]

	function getPreviousUID() {
		$last = false;
		if (false == $this->allRevisionsLoaded) { $this->getAllRevisions(); }
		foreach($this->allRevisions as $key => $row) {				// for each revision
			if ($row['UID'] == $this->UID) { return $last; }		// if this one is found
			$last = $row['UID'];
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	find UID of next version of this wiki article or talk page
	//----------------------------------------------------------------------------------------------
	//returns: UID of next revision, false if not found [string][bool]

	function getNextUID() {
		$next = false;
		if (false == $this->allRevisionsLoaded) { $this->getAllRevisions(); }
		foreach($this->allRevisions as $key => $row) {			// for each revision
			if (true == $next) { return $row['UID']; }			// last one matches this
			if ($row['UID'] == $this->UID) { $next = true; }	// if this one is found
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	find all revisions to this wiki article or talk page, make list at $this->allRevisions
	//----------------------------------------------------------------------------------------------
	//; note that we only load UID, editedBy, editedOn and reason as there may hudnreds of edits
	//TODO: this can be more standard and kapenta-ish

	function getAllRevisions() {
		global $kapenta;
		$this->allRevisions = array();
		if ('' == $this->articleUID) { return false; }

		$conditions = array();
		$conditions[] = "articleUID='" . $kapenta->db->addMarkup($this->articleUID) . "'";

		$fields = 'UID, reason, editedBy, editedOn';

		$range = $kapenta->db->loadRange('wiki_revision', $fields, $conditions, 'editedOn ASC');
		foreach($range as $row) { $this->allRevisions[] = $row; }
		$this->allRevisionsLoaded = true;
		return true;
	}

}

?>
