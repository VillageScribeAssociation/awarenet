<?

//--------------------------------------------------------------------------------------------------
//	object for recording record edits
//--------------------------------------------------------------------------------------------------

class Sync {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	var $noSync = 'sync|changes';	// tables which are not synced

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Sync($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('sync', $uid, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		return true;
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
		dbSave($this->data, $this->dbSchema);	 // consider alternate save mechanism
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'sync';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'table' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(30)',	
			'timestamp' => 'VARCHAR(20)',
			'hash' => 'VARCHAR(42)'
		);

		$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10', 'table' => '6');

		$dbSchema['nodiff'] = array('UID', 'table', 'refUID', 'timestamp', 'hash');
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
		$report = "<h3>Installing Sync (sync) Model</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (dbTableExists('sync') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created sync table and indices...<br/>';
		} else {
			$this->report .= 'sync table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a sync record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('sync', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	list sync-able tables
	//----------------------------------------------------------------------------------------------

	function listTables() {
		$retVal = array();
		$tables = dbListTables();
		$noSync = explode('|', $this->noSync);
		foreach($tables as $table) {
			if (in_array($table, $noSync) == false) { $retVal[] = $table; }
		}
		return $retVal;
	}
	
}
?>
