<?

//--------------------------------------------------------------------------------------------------
//	object for forum thread replies
//--------------------------------------------------------------------------------------------------

class ForumReply {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function ForumReply($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['createdBy'] = $user->data['UID'];
		$this->data['createdOn'] = mysql_datetime(time());
		$this->data['editedBy'] = $user->data['UID'];
		$this->data['editedOn'] = mysql_datetime(time());
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('forumreplies', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('forumreplies', $d['UID'], $d['title'], 'forumreplies');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

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

		$ary['summary'] = strip_tags($ary['description']);
		$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';
	
		//------------------------------------------------------------------------------------------
		//	format for WYSWYG editor
		//------------------------------------------------------------------------------------------

		$ary['contentJs'] = $ary['content'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);
	
		$ary['contentHtml'] = $ary['content'];

		$ary['summary'] = substr(strip_tags($ary['content']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];		
		$ary['userRa'] = $model->data['recordAlias'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

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
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete any images associated with this thread
		//------------------------------------------------------------------------------------------
		// TODO

		//------------------------------------------------------------------------------------------
		//	delete any files associated with this thread
		//------------------------------------------------------------------------------------------
		// TODO

		//------------------------------------------------------------------------------------------
		//	delete all images associated with this thread
		//------------------------------------------------------------------------------------------		
		raDeleteAll('forumreplies', $this->data['UID']);
		dbDelete('forumreplies', $this->data['UID']);
	}

}

?>
