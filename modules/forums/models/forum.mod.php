<?

//--------------------------------------------------------------------------------------------------
//*	object for user forums
//--------------------------------------------------------------------------------------------------
//+	A very limited phpBB clone.  Forums may be general bound to a school.  Everyone can view forums,
//+	but posting may be limited.  Special members - moderators - can delete unwanted posts, change
//+	the forum's title and description, add more moderators, etc.
//+
//+	What type a forum is is dependant on the 'school' field, if it contains the UID of a school,
//+	it is bound to that school, if blank, it is general, if 'private' it is limited to whomever
//+	is in the 'members' field

require_once($installPath . 'modules/forums/models/forumthread.mod.php');
require_once($installPath . 'modules/forums/models/forumreply.mod.php');

class Forum {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	var $moderators;	// array of user UIDs
	var $members;		// array of user UIDs
	var $banned;		// array of user UIDs

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of a forum [string]

	function Forum($raUID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Forum ' . $this->data['UID'];
		$this->data['moderators'] = $user->data['UID'];
		$this->expandData();
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of a forum [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		$ary = dbLoadRa('forums', $raUID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
		$this->expandData();
	}

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->collapseData();

		$this->data['recordAlias'] = raSetAlias(	'forums', $this->data['UID'], 
													$this->data['title'], 'forums'	);

		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand moderators, members, banned
	//----------------------------------------------------------------------------------------------

	function expandData() {
		$this->moderators = array();	
		$modAry = explode("|", $this->data['moderators']);
		foreach($modAry as $modUID) { if ($modUID != '') { $this->moderators[] = $modUID; } }

		$this->members = array();
		$memberAry = explode("|", $this->data['members']);
		foreach($memberAry as $memUID) { if ($memUID != '') { $this->members[] = $memUID; } }

		$this->banned = array();
		$bannedAry = explode("|", $this->data['banned']);
		foreach($bannedAry as $banUID) { if ($banUID != '') { $this->banned[] = $banUID; } }
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse back to flat record
	//----------------------------------------------------------------------------------------------

	function collapseData() {
		$this->data['moderators'] = implode('|', $this->moderators);
		$this->data['members'] = implode('|', $this->members);
		$this->data['banned'] = implode('|', $this->banned);
	}


	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'forums';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'school' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'weight' => 'VARCHAR(10)',
			'moderators' => 'TEXT',
			'members' => 'TEXT',
			'banned' => 'TEXT',
			'threads' => 'VARCHAR(30)',
			'replies' => 'VARCHAR(30)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'school' => 10, 'recordAlias' => '20' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ary = $this->data;

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
		if ($user->data['ofGroup'] == 'admin') { $auth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('forums', 'show', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%forums/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[see all threads &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%forums/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%forums/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new forums]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%forums/addchild/" . $ary['recordAlias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child forums]</a>";  
			$ary['delUrl'] = "%%serverPath%%forums/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete forums]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['forumTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		$ary['summary'] = strip_tags(strip_blocks($ary['description']));
		$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';	
		$ary['summary'] = substr(strip_tags($ary['description']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new User($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];		
		$ary['userRa'] = $model->data['recordAlias'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
		$report = "<h3>Installing forums Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create forums table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('forums') == false) {	
			echo "installing forums module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created forums table and indices...<br/>';
		} else {
			$this->report .= 'forums table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete all threads //TODO: replace this with an event hander (forums_deleted)
		//------------------------------------------------------------------------------------------
		$sql = "select * from forumthreads where forum='" . sqlMarkup($this->data['UID']) . "'";
		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$thread = new ForumThread();
			$thread->loadArray(sqlRMArray($row));
			$thread->delete();
		}

		//------------------------------------------------------------------------------------------
		//	delete this record
		//------------------------------------------------------------------------------------------		
		raDeleteAll('forums', $this->data['UID']);
		dbDelete('forums', $this->data['UID']);
	}

}

?>
