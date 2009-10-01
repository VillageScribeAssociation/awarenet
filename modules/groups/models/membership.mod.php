<?

//--------------------------------------------------------------------------------------------------
//	index table
//--------------------------------------------------------------------------------------------------
//	group type could be Team/Club/Society, etc

class GroupMembership {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function GroupMembership($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('groupmembers', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { echo $verify; return $verify; }

		$d = $this->data;
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

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
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'groupmembers';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'groupUID' => 'VARCHAR(30)',	
			'userUID' => 'VARCHAR(30)',
			'position' => 'VARCHAR(20)',
			'admin' => 'VARCHAR(10)',
			'joined' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 'group' => '10', 'user' => '10');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('groupmembers', $this->data['UID']);
	}

}

?>
