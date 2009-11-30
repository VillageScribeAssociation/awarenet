<?

//--------------------------------------------------------------------------------------------------
//	object for recording deleted items, so that they are not respawned by the sync
//--------------------------------------------------------------------------------------------------
//	note that file/image deletions are also stroed in this table, with 'refTable' set to
// 'localfile', and the refUID to relative filename.

class DeletedItem {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function DeletedItem($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('delitems', $uid, 'true');
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
		$dbSchema['table'] = 'delitems';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'refTable' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(255)',	
			'timestamp' => 'VARCHAR(20)'
		);

		$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10', 'refTable' => '6');
		$dbSchema['nodiff'] = array('UID', 'table', 'refUID', 'timestamp');
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
		$report = "<h3>Installing Deleted Items Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (dbTableExists('delitems') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created deleted items table and indices...<br/>';
		} else {
			$this->report .= 'deleted items table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a sync record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('delitems', $this->data['UID']);
	}
	
}
?>
