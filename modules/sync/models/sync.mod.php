<?

//--------------------------------------------------------------------------------------------------
//*	object for recording sync notices
//--------------------------------------------------------------------------------------------------
//+	when an event occurs or we are notified of one by a peer it is stored in the sync table until
//+	successfully rebroadcast to all peers which need to know about it.  Retrying periodically.
//+	
//+	Database fields mean the following:
//+	UID 		- a unique ID
//+	source 		- peer we recieved this from, 'self' if the event occured on this server
//+	type		- type of event, eg 'dbupdate', 'dbdelete', etc
//+	data		- serialized, depends on type
//+	peer		- UID of peer to broadcast this to
//+	status		- locked / failed
//+	received	- when this entry was created
//+	timestamp	- of last attempt to pass on to peer

class Sync {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of object recording deleted item (not the UID of the item itself) [string]

	function Sync($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['UID'] = createUID();
		$this->data['received'] = time();
		$this->data['timestamp'] = time();
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a sync notice [string]
	//returns: true if object is found and loaded, otherwise false [bool]

	function load($UID) {
		$ary = dbLoad('sync', $UID, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		return true;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		dbSave($this->data, $this->dbSchema);	 // consider alternate save mechanism
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]
	//,	note that this also exists in /core/sync.inc.php, copy any changes there

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
	//.	serialize this object as an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		$ary = $this->data;			
		$ary['datahtml'] = wordwrap($ary['data'], 40, "\n", true);
		$ary['datahtml'] = str_replace("<", "&lt;", $ary['datahtml']);
		$ary['datahtml'] = str_replace(">", "&gt;", $ary['datahtml']);
		$ary['datahtml'] = str_replace("\n", "<br/>\n", $ary['datahtml']);
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.php

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
	//.	delete the current sync notice from the database
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('sync', $this->data['UID']);
	}
	
}
?>
