<?

//--------------------------------------------------------------------------------------------------
//*	object for personal messages, like webmail
//--------------------------------------------------------------------------------------------------

class Message {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database table structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of group membership index record [string]

	function Message($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] = 'New Message ' . $this->data['UID'];
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a group membership [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		$ary = dbLoad('messages', $UID);
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
		$dbSchema['table'] = 'messages';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'owner' => 'VARCHAR(30)',
			'folder' => 'VARCHAR(30)',
			'fromUID' => 'VARCHAR(30)',
			'toUID' => 'VARCHAR(30)',
			'cc' => 'TEXT',
			'title' => 'VARCHAR(255)',
			're' => 'VARCHAR(30)',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME', 
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)' );

		$dbSchema['indices'] = array('UID' => '10', 'fromUID' => 10, 'toUID' => '10' );
		$dbSchema['nodiff'] = array('UID');
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

		$ary['editUrl'] = '';		$ary['editLink'] = '';
		$ary['viewUrl'] = '';		$ary['viewLink'] = '';
		$ary['newUrl'] = '';		$ary['newLink'] = '';
		$ary['delUrl'] = '';		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ($user->data['ofGroup'] == 'admin') { $auth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('messages', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%messages/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[see all threads &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%messages/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%messages/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new messages]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%messages/addchild/" . $ary['recordAlias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child messages]</a>";  
			$ary['delUrl'] = "%%serverPath%%messages/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete messages]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['messageTitle'] = $ary['title'];

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
		$report = "<h3>Installing Messages Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create messages table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('messages') == false) {	
			echo "installing messages module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created messages table and indices...<br/>';
		} else {
			$this->report .= 'messages table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//------------------------------------------------------------------------------------------
		//	delete any images associated with this message
		//------------------------------------------------------------------------------------------
		//TODO

		//------------------------------------------------------------------------------------------
		//	delete this record
		//------------------------------------------------------------------------------------------		
		raDeleteAll('messages', $this->data['UID']);
		dbDelete('messages', $this->data['UID']);
	}

}

?>
