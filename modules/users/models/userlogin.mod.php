<?

//--------------------------------------------------------------------------------------------------
//*	object to represent loghged in user sessions
//--------------------------------------------------------------------------------------------------
//+	this is used by features such as the chat, which need to know whether a user is logged in and 
//+	to which peer.

class UserLogin {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database structure [array]

	var $maxAge = 300;	// maximum age of user login session, in seconds [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: userUID - UID of a user [string]

	function UserLogin($userUID = '') {
		global $serverPath;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['lastseen'] = time();
		$this->data['logintime'] = mysql_datetime();
		$this->data['serverurl'] = $serverPath;
		if ($userUID != '') { $this->load($userUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a login session object by user UID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]

	function load($userUID) {
		$sql = "select * from userlogin where userUID='" . sqlMarkup($userUID) . "'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) {
			$row = dbFetchAssoc($result);
			$row = sqlRMArray($row);
			$this->loadArray($row);
			return true;

		} else { return false; }
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]
	
	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		dbSave($this->data, $this->dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (strlen($this->data['userUID']) < 5) { $verify .= "User UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'userlogin';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'userUID' => 'VARCHAR(255)',
			'serverurl' => 'VARCHAR(255)',
			'logintime' => 'DATETIME',
			'lastseen' => 'VARCHAR(20)',				
			'editedOn' => 'DATETIME',	
			'editedBy' => 'VARCHAR(30)'
		);

		$dbSchema['indices'] = array('UID' => '10', 'userUID' => '10');
		$dbSchema['nodiff'] = array('UID', 'userUID', 'serverurl', 'logintime', 'lastseen', 'editedOn', 'editedBy');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object as an array
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
		$ary = $this->data;			
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.php

	function install() {
		$report = "<h3>Installing User Login  Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (dbTableExists('userlogin') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created userlogin table and indices...<br/>';
		} else {
			$this->report .= 'userlogin table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current user login session object
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('userlogin', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//.	clear old entries from the userlogin table
	//----------------------------------------------------------------------------------------------

	function clearOldEntries() {
		global $serverPath;
		$userlogin = dbLoadRange('userlogin', '*', '', '', '', '');
		foreach($userlogin as $row) {
			if (($row['serverUrl'] == $serverPath) && (time() > ($row['lastseen'] + $this->maxAge)))
				{ dbDelete('userlogin', $row['UID']); }
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if there is already an entry for this user
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if there is a record of a current session, otherwise false [bool]

	function inList($userUID) {
		$sql = "select * from userlogin where userUID='" . sqlMarkup($userUID) . "'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	update lastseen to current time
	//----------------------------------------------------------------------------------------------

	function updateLastSeen() {
		dbUpdateQuiet('userlogin', $this->data['UID'], 'lastseen', time());
	}
	
}
?>
