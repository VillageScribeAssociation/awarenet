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
		global $db, $kapenta;
		$this->dbSchema = $this->getDbSchema();
		$this->data = $db->makeBlank($this->dbSchema);
		$this->UID = $kapenta->createUID();
		$this->lastseen = time();
		$this->logintime = $db->datetime();
		$this->serverurl = $serverPath;
		if ($userUID != '') { $this->load($userUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a login session object by user UID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]

	function load($userUID) {
		global $db;

		$sql = "select * from Users_Login where userUID='" . $db->addMarkup($userUID) . "'";
		$result = $db->query($sql);
		if ($db->numRows($result) > 0) {
			$row = $db->fetchAssoc($result);
			$row = $db->rmArray($row);
			$this->loadArray($row);
			return true;

		} else { return false; }
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]
	//TODO: change to standard design pattern
	
	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
	global $db;

		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		$db->save($this->data, $this->dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		if (strlen($this->UID) < 5) { $verify .= "UID not present.\n"; }
		if (strlen($this->userUID) < 5) { $verify .= "User UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['model'] = 'Users_Login';
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
	global $db;

		$report = "<h3>Installing User Login  Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (false == $db->tableExists('Users_Login')) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created Users_Login table and indices...<br/>';
		} else {
			$this->report .= 'Users_Login table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current user login session object
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $db;
		$db->delete('users', 'Users_Login', $this->UID);
	}

	//----------------------------------------------------------------------------------------------
	//.	clear old entries from the Users_Login table
	//----------------------------------------------------------------------------------------------

	function clearOldEntries() {
		global $db, $serverPath;
		$userlogin = $db->loadRange('Users_Login', '*', '', '', '', '');
		foreach($userlogin as $row) {
			if (($row['serverUrl'] == $serverPath) && (time() > ($row['lastseen'] + $this->maxAge)))
				{ $db->delete('users', 'Users_Login', $row['UID']); }
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if there is already an entry for this user
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//returns: true if there is a record of a current session, otherwise false [bool]

	function inList($userUID) {
		global $db;

		$sql = "select * from Users_Login where userUID='" . $db->addMarkup($userUID) . "'";
		$result = $db->query($sql);
		if ($db->numRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	update lastseen to current time
	//----------------------------------------------------------------------------------------------

	function updateLastSeen() {
		global $db;
		$db->updateQuiet('Users_Login', $this->UID, 'lastseen', time());
	}
	
}
?>
