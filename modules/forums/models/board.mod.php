<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent forum boards
//--------------------------------------------------------------------------------------------------
//+	A very limited phpBB clone.  Forums may be general bound to a school.  Everyone can view forums,
//+	but posting may be limited.  Special members - moderators - can delete unwanted posts, change
//+	the forum's title and description, add more moderators, etc.
//+
//+	What type a forum is is dependant on the 'school' field, if it contains the UID of a school,
//+	it is bound to that school, if blank, it is general, if 'private' it is limited to whomever
//+	is in the 'members' field

class Forums_Board {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;			//_	currently loaded database record [array]
	var $dbSchema;		//_	database table definition [array]
	var $loaded;		//_	set to true when an object has been loaded [bool]

	var $UID;			//_ UID [string]
	var $school;		//_ varchar(50) [string]
	var $title;			//_ varchar(255) [string]
	var $description;	//_ wyswyg [string]
	var $weight;		//_ varchar(10) [string]
	var $threads;		//_ varchar(30) [string]
	var $replies;		//_ varchar(30) [string]
	var $createdOn;		//_ datetime [string]
	var $createdBy;		//_ ref:Users_User [string]
	var $editedOn;		//_ datetime [string]
	var $editedBy;		//_ ref:Users_User [string]
	var $alias;			//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Board object [string]

	function Forums_Board($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Forum ' . $this->UID;		// default title
			$this->description = '(describe your forum here)';	// hmmm...
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Board object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Board object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->school = $ary['school'];
		$this->title = $ary['title'];
		$this->description = $ary['description'];
		$this->weight = (int)$ary['weight'];
		$this->threads = $ary['threads'];
		$this->replies = $ary['replies'];
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
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('forums', 'forums_board', $this->UID, $this->UID);
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
		$dbSchema['module'] = 'forums';
		$dbSchema['model'] = 'forums_board';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'school' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'weight' => 'VARCHAR(10)',
			'threads' => 'VARCHAR(30)',
			'replies' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'school' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array('threads', 'replies');

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
			'title' => $this->title,
			'description' => $this->description,
			'weight' => (int)$this->weight . '',
			'threads' => $this->threads,
			'replies' => $this->replies,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand moderators, members, banned //TODO
	//----------------------------------------------------------------------------------------------

	function expandData() {
		$this->moderators = array();	
		$modAry = explode("|", $this->moderators);
		foreach($modAry as $modUID) { if ($modUID != '') { $this->moderators[] = $modUID; } }

		$this->members = array();
		$memberAry = explode("|", $this->members);
		foreach($memberAry as $memUID) { if ($memUID != '') { $this->members[] = $memUID; } }

		$this->banned = array();
		$bannedAry = explode("|", $this->banned);
		foreach($bannedAry as $banUID) { if ($banUID != '') { $this->banned[] = $banUID; } }
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse back to flat record //TODO
	//----------------------------------------------------------------------------------------------

	function collapseData() {
		$this->moderators = implode('|', $this->moderators);
		$this->members = implode('|', $this->members);
		$this->banned = implode('|', $this->banned);
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
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['addChildUrl'] = '';
		$ary['addChildLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ('admin' == $user->role) { $auth = true; }
		if ($user->UID == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if ($user->authHas('forums', 'forums_board', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%forums/' . $ary['alias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[see all threads &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%forums/edit/' . $ary['alias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%forums/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new forums]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%forums/addchild/" . $ary['alias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child forums]</a>";  
			$ary['delUrl'] = "%%serverPath%%forums/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete forums]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['createdOn']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['forumTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------
		$ary['summary'] = $theme->makeSummary($ary['description']);

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users_User($ary['createdBy']);
		$ary['userName'] = $model->firstname . ' ' . $model->surname;		
		$ary['userRa'] = $model->alias;
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
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

}

?>
