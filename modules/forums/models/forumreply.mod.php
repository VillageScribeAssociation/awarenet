<?

//--------------------------------------------------------------------------------------------------
//*	object for forum thread replies
//--------------------------------------------------------------------------------------------------

class ForumReply {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of a reply to a thread [string]

	function ForumReply($raUID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of a a reply to a thread [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		$ary = dbLoadRa('forumreplies', $raUID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->data['recordAlias'] = raSetAlias(	'forumreplies', $this->data['UID'], 
													$this->data['title'], 'forumreplies'	);

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
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'forumreplies';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'forum' => 'VARCHAR(30)',
			'thread' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 
									'forum' => 10,
									'thread' => 10,  
									);

		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

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

		if (authHas('forumreplies', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%forumreplies/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%forumreplies/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%forumreplies/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new forumreplies]</a>";  
			$ary['delUrl'] = "%%serverPath%%forumreplies/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete forumreplies]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['createdOn']));

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		$ary['summary'] = strip_tags(strip_blocks($ary['description']));
		$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';
		$ary['summary'] = substr(strip_tags($ary['content']), 0, 400) . '...';

		$ary['contentHtml'] = $ary['content'];

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
		$report = "<h3>Installing Fourm Replies Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create forumreplies table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('forumreplies') == false) {	
			echo "installing forumreplies module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created forumreplies table and indices...<br/>';
		} else {
			$this->report .= 'forumreplies table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete any images associated with this reply
		//------------------------------------------------------------------------------------------
		// TODO: event for this (object_deleted?)

		//------------------------------------------------------------------------------------------
		//	delete all images associated with this thread
		//------------------------------------------------------------------------------------------		
		raDeleteAll('forumreplies', $this->data['UID']);
		dbDelete('forumreplies', $this->data['UID']);
	}

}

?>
