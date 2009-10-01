<?

//--------------------------------------------------------------------------------------------------
//	object for managing records of announcements
//--------------------------------------------------------------------------------------------------
//	announcements are owned by a record on another module (schools/groups) and notifications
//	are sent when they are produced.  

class Announcement {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Announcement($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Announcement';
		$this->data['createdOn'] = mysql_datetime();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('announcements', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//	save a record (and send notifications if they have not been sent)
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->data['recordAlias'] = raSetAlias('announcements', $this->data['UID'], 
												$this->data['title'], 'announcements');

		if (($this->data['title'] != '') AND ($this->data['title'] != 'New Announcement')) {
			$this->data['notifications'] = 'sent';
		}

		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'announcements';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'refModule' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'notifications' => 'VARCHAR(10)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		global $user;
		$ary = $this->data;
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

		$hasEditAuth = $this->hasEditAuth($user->data['UID']);

		if (authHas('announcements', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%announcements/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('announcements', 'edit', $this->data) && $hasEditAuth) {
			$ary['editUrl'] =  '%%serverPath%%announcements/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('announcements', 'edit', $this->data)  && $hasEditAuth) {
			$ary['delUrl'] =  '%%serverPath%%announcements/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('announcements', 'new', $this->data)  && $hasEditAuth) { 
			$ary['newUrl'] = "%%serverPath%%announcements/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new announcement]</a>"; 
		}

		$createdBy = new Users($ary['createdBy']);
		$ary['userUrl'] = '/users/profile/' . $createdBy->data['recordAlias'];
		$ary['userName'] = $createdBy->data['firstname'] . ' ' . $createdBy->data['surname'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userName'] . "</a>";

		//------------------------------------------------------------------------------------------
		//	namespace collision
		//------------------------------------------------------------------------------------------

		$ary['anTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	time
		//------------------------------------------------------------------------------------------

		$ary['time'] = mysql_datetime($ary['createdOn']);
	
		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = str_replace(">\n", ">", trim($ary['content']));
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
		$ary['summary'] = substr(strip_tags(strip_blocks($ary['content'])), 0, 800) . '...';
		$ary['summarynav'] = substr(strip_tags(strip_blocks($ary['content'])), 0, 200) . '...';

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		$ary['contentJs'] = $ary['content'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Announcements Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create announcements table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('announcements') == false) {	
			echo "installing announcements module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created announcements table and indices...<br/>';
		} else {
			$this->report .= 'announcements table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		// notify other modules
		$args = array('module' => 'moblog', 'UID' => $this->data['UID']);
		callbackSendAll('record_delete', $args);
		
		raDeleteAll('announcements', $this->data['UID']);
		dbDelete('announcements', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	check if a user is authorised to edit this document
	//----------------------------------------------------------------------------------------------

	function hasEditAuth($userUID) {
		$cb = "[[:" . $this->data['refModule'] . "::haseditauth::refUID=" . $this->data['refUID'] . ":]]";
		$result = expandBlocks($cb, '');
		if ('yes' == $result) { return true; }
		return false;
	}

}

?>
