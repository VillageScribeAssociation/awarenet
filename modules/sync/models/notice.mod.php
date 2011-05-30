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

class Sync_Notice {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $source;			//_ varchar(33) [string]
	var $type;				//_ varchar(50) [string]
	var $ndata;				//_ text [string]
	var $peer;				//_ varchar(33) [string]
	var $status;			//_ varchar(30) [string]
	var $failures;			//_ VARCHAR(10) [int]
	var $received;			//_ varchar(30) [string]
	var $timestamp;			//_ varchar(30) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Notice object [string]

	function Sync_Notice($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->received = time();						// set receipt time
			$this->timestamp = time();						// set creation time
			$this->failures = 0;
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Notice object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Notice object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->source = $ary['source'];
		$this->type = $ary['type'];
		$this->ndata = $ary['ndata'];
		$this->peer = $ary['peer'];
		$this->status = $ary['status'];
		$this->failures = (int)$ary['failures'];
		$this->received = $ary['received'];
		$this->timestamp = $ary['timestamp'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases, $kapenta;
		$kapenta->logSync("Sync_Notification::save()<br/>");
		$report = $this->verify();
		if ('' != $report) {
			$kapenta->logSync("Sync_Notification::save() FAILED $report <br/>");
			return $report;
		}
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) {
			$kapenta->logSync("Sync_Notification::save() FAILED Database Error <br/>");
			return "Database error.<br/>\n";
		}
		$kapenta->logSync("Sync_Notification::save() FINISHED<br/>");
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }

		//$peer = new Sync_Server($this->peer);
		//if (false == $peer->loaded) { $report .= "No such peer."; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'sync';
		$dbSchema['model'] = 'sync_notice';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'source' => 'VARCHAR(33)',
			'type' => 'VARCHAR(50)',
			'ndata' => 'TEXT',
			'peer' => 'VARCHAR(33)',
			'status' => 'VARCHAR(30)',
			'failures' => 'VARCHAR(10)',
			'received' => 'VARCHAR(30)',
			'timestamp' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'source' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will not be kept for any field
		$dbSchema['nodiff'] = array(
			'UID',
			'source',
			'type',
			'ndata',
			'peer',
			'status',
			'failures',
			'received',
			'timestamp',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'source' => $this->source,
			'type' => $this->type,
			'ndata' => $this->ndata,
			'peer' => $this->peer,
			'status' => $this->status,
			'failures' => $this->failures . '',
			'received' => $this->received,
			'timestamp' => $this->timestamp,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('sync', 'sync_notice', 'view', $ext['UID'])) {
			$ext['viewUrl'] = 'TODO: this';
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('sync', 'sync_notice', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%~%serverPath%~%Sync/editnotice/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('sync', 'sync_notice', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%~%serverPath%~%Sync/delnotice/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}
	
}

?>
