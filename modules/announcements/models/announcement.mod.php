<?

//--------------------------------------------------------------------------------------------------
//*	object for managing records of announcements
//--------------------------------------------------------------------------------------------------
//+	announcements are owned by a record on another module (schools/groups) and notifications
//+	are sent when they are produced.  

class Announcements_Announcement {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*-* [string]
	var $title;				//_ title [string]
	var $content;			//_ wyswyg [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]


	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of an Announcement object [string]

	function Announcements_Announcement($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);			// initialize
			$this->title = "New Announcement " . $this->UID;
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of an announcement record [string]
	//returns: true on success, false on failure [bool]

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Announcement object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Announcement object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
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
		$this->alias = $aliases->create('announcements', 'announcements_announcement', $this->UID, $this->UID);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}


	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';

		if (strlen($this->UID) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'announcements';
		$dbSchema['model'] = 'announcements_announcement';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
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
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'title' => $this->title,
			'content' => $this->content,
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
		global $db, $user, $theme;
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

		//$hasEditAuth = $this->hasEditAuth($user->UID);

		if (true == $user->authHas('announcements', 'announcements_announcement', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%announcements/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $user->authHas('announcements', 'announcements_announcement', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%announcements/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $user->authHas('announcements', 'announcements_announcement', 'delete', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%announcements/confirmdelete/UID_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (true == $user->authHas('announcements', 'announcements_announcement', 'new')) { 
			$ary['newUrl'] = "%%serverPath%%announcements/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new announcement]</a>"; 
		}

		$createdBy = new Users_User($ary['createdBy']);
		$ary['userUrl'] = '/users/profile/' . $createdBy->alias;
		$ary['userName'] = $createdBy->firstname . ' ' . $createdBy->surname;
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";

		//------------------------------------------------------------------------------------------
		//	namespace collision
		//------------------------------------------------------------------------------------------
		$ary['anTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	time
		//------------------------------------------------------------------------------------------
		$ary['time'] = $db->datetime(strtotime($ary['createdOn']));
	
		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------
		$ary['contentHtml'] = str_replace(">\n", ">", trim($ary['content']));
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
		$ary['summary'] = $theme->makeSummary($ary['content'], 800);
		$ary['summarynav'] = $theme->makeSummary($ary['content'], 200);

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
		if (false == $db->delete($this->UID, $this->dbSchema))
			{ return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if a user is authorised to edit this document  TODO: upgrade this, permission
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if current user is authorized to edit this record, else false [bool]

	function hasEditAuth($userUID) {
		global $theme;
		$cb = "[[:" . $this->refModule . "::haseditauth::refUID=" . $this->refUID . ":]]";
		$result = $theme->expandBlocks($cb, '');
		if ('yes' == $result) { return true; }
		return false;
	}

}

?>
