<?

//--------------------------------------------------------------------------------------------------
//*	index table for group membership (users <-> groups)
//--------------------------------------------------------------------------------------------------

class GroupMembership {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of group membership index record [string]

	function GroupMembership($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a group membership [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		$ary = dbLoad('groupmembers', $UID);
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
		if ($verify != '') { echo $verify; return $verify; }
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }
		if (dbRecordExists('users', $this->data['userUID']) == false) 
			{ $verify .= "Member does not exist."; }
		if (dbRecordExists('groups', $this->data['groupUID']) == false) 
			{ $verify .= "Group does not exist."; }

		return $verify;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'groupmembers';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'groupUID' => 'VARCHAR(30)',	
			'userUID' => 'VARCHAR(30)',
			'position' => 'VARCHAR(20)',
			'admin' => 'VARCHAR(10)',
			'joined' => 'DATETIME',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)' );

		$dbSchema['indices'] = array('UID' => '10', 'group' => '10', 'user' => '10');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {
		//TODO: fire off some events here, maybe some notifications
		dbDelete('groupmembers', $this->data['UID']);
	}

}

?>
