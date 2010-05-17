<?

//--------------------------------------------------------------------------------------------------
//*	object for forum threads
//--------------------------------------------------------------------------------------------------

class ForumThread {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database table structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of a forum thread [string]

	function ForumThread($raUID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Thread ' . $this->data['UID'];
		$this->data['updated'] = mysql_datetime();
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of an announcement record [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		$ary = dbLoadRa('forumthreads', $raUID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$ra = raSetAlias('forumthreads', $this->data['UID'], $this->data['title'], 'forumthreads');
		$this->data['recordAlias'] = $ra;

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
		$dbSchema['table'] = 'forumthreads';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'forum' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'replies' => 'VARCHAR(10)',
			'sticky' => 'VARCHAR(10)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'updated' => 'DATETIME',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',			
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 
									'forum' => 10, 
									'recordAlias' => '20', 
									'updated' => '' );

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

		if (authHas('forumthreads', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%forumthreads/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%forumthreads/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%forumthreads/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new forumthreads]</a>";  
			$ary['delUrl'] = "%%serverPath%%forumthreads/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete forumthreads]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['threadTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		$ary['summary'] = strip_blocks(strip_tags($ary['content']));
		$ary['summary'] = substr($ary['summary'], 0, 300) . '...';
	
	
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['content']);

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
		$report = "<h3>Installing forumthreads Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create forumthreads table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('forumthreads') == false) {	
			echo "installing forumthreads module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created forumthreads table and indices...<br/>';
		} else {
			$this->report .= 'forumthreads table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current thread
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete images, files, etc
		//------------------------------------------------------------------------------------------
		// TODO: object deleted?

		//------------------------------------------------------------------------------------------
		//	delete this record
		//------------------------------------------------------------------------------------------		
		raDeleteAll('forumthreads', $this->data['UID']);
		dbDelete('forumthreads', $this->data['UID']);
	}

}

?>
