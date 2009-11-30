<?

//--------------------------------------------------------------------------------------------------
//	object for recording sync notices
//--------------------------------------------------------------------------------------------------
//	when an event occurs or we are notified on one by a peer it is stored in the sync table until
//	successfully rebroadcast to all peers which need to know about it.  Retrying periodically.
//	
//	Database fields mean the following:
//	UID 		- a unique ID
//	source 		- peer we recieved this from, 'self' if the event occured on this server
//	type		- type of event, eg 'dbupdate', 'dbdelete', etc
//	data		- serialized, depends on type
//	peer		- UID of peer to broadcast this to
//	status		- locked / failed
//	received	- when this entry was created
//	timestamp	- of last attempt to pass on to peer

class Sync {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Sync($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['received'] = time();
		$this->data['timestamp'] = time();
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
	//	note that this also exists in /core/sync.inc.php, copy any changes there

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'sync';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'source' => 'VARCHAR(30)',
			'type' => 'VARCHAR(50)',
			'data' => 'TEXT',	
			'peer' => 'VARCHAR(30)',
			'status' => 'VARCHAR(30)',
			'received' => 'VARCHAR(30)',
			'timestamp' => 'VARCHAR(20)'
		);

		$dbSchema['indices'] = array('UID' => '10');
		$dbSchema['nodiff'] = array('UID', 'source', 'type', 'data', 'peer', 'status', 'received', 'timestamp');
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
	
}
?>
