<?

//--------------------------------------------------------------------------------------------------
//	object for recording files which we are currently downloading
//--------------------------------------------------------------------------------------------------

class Download {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	var $maxAge = 3600;
	var $numDownloads = 3;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Download($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['timestamp'] = time();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('downloads', $uid, 'true');
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
		dbSave($this->data, $this->dbSchema);	 // consider alternate save mechanism
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (strlen(trim($this->data['filename'])) < 5) { $verify .= "filename not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'downloads';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'filename' => 'VARCHAR(255)',
			'hash' => 'VARCHAR(255)',	
			'status' => 'VARCHAR(20)',	
			'timestamp' => 'VARCHAR(20)'
		);

		$dbSchema['indices'] = array('UID' => '10', 'filename' => '10');
		$dbSchema['nodiff'] = array('UID', 'filename', 'hash', 'timestamp');
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
		$report = "<h3>Installing Downloads Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//------------------------------------------------------------------------------------------
		if (dbTableExists('downloads') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created downloads table and indices...<br/>';
		} else {
			$this->report .= 'downloads table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a sync record
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('downloads', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	clear old entries from the downloads table
	//----------------------------------------------------------------------------------------------

	function clearOldEntries() {
		$downloads = dbLoadRange('downloads', '*', '', '', '', '');
		foreach($downloads as $row) {
			if (time() > ($row['timestamp'] + $this->maxAge)) { dbDelete('downloads', $row['UID']); }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	discover if the is already in download queue
	//----------------------------------------------------------------------------------------------

	function inList($fileName) {
		$sql = "select * from downloads where filename='" . sqlMarkup($fileName) . "'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	discover if we're currently busy with too many files
	//----------------------------------------------------------------------------------------------

	function maxDownloads() {
		$sql = "select UID from downloads where status='searching'";
		$result = dbQuery($sql);
		if (dbNumRows($result) > $this->numDownloads) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	get next download
	//----------------------------------------------------------------------------------------------

	function getNextDownload() {
		$sql = "select * from downloads where status='wait' order by rand()";
		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) {
			$row = dbFetchAssoc($result);
			$row = sqlRMArray($row);
			return $row['UID'];

		} else { return false; }
	}
	
}
?>
