<?

//--------------------------------------------------------------------------------------------------
//	object for recording files which we are currently downloading
//--------------------------------------------------------------------------------------------------

class UserLogin {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	var $maxAge = 300;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function UserLogin($userUID = '') {
		global $serverPath;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['lastseen'] = time();
		$this->data['logintime'] = mysql_datetime();
		$this->data['serverurl'] = $serverPath;
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

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
	
	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		dbSave($this->data, $this->dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (strlen($this->data['userUID']) < 5) { $verify .= "User UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need, can't imagine this will be used
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;			
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

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
	//	delete a sync record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('userlogin', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	clear old entries from the userlogin table
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
	//	discover if there is already an entry for this user
	//----------------------------------------------------------------------------------------------

	function inList($userUID) {
		$sql = "select * from userlogin where userUID='" . sqlMarkup($userUID) . "'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	update lastseen to current time
	//----------------------------------------------------------------------------------------------

	function updateLastSeen() {
		dbUpdateQuiet('userlogin', $this->data['UID'], 'lastseen', time());
	}
	
}
?>
