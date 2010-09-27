<?

//--------------------------------------------------------------------------------------------------
//*	object for recording files which this peer is currently downloading
//--------------------------------------------------------------------------------------------------

class Sync_Download {
	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;					//_	currently loaded database record [array]
	var $dbSchema;				//_	database table definition [array]
	var $loaded;				//_	set to true when an object has been loaded [bool]

	var $UID;					//_ UID [string]
	var $filename;				//_ varchar(255) [string]
	var $hash;					//_ varchar(255) [string]
	var $status;				//_ varchar(30) [string]
	var $timestamp;				//_ varchar(30) [string]
	var $createdOn;				//_ datetime [string]
	var $createdBy;				//_ ref:Users_User [string]
	var $editedOn;				//_ datetime [string]
	var $editedBy;				//_ ref:Users_User [string]

	var $maxAge = 3600;			// number of seconds before the download is abandoned [int]
	var $numDownloads = 3;		// maximum number of downloads [int]


	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Download object [string]

	function Sync_Download($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->timestamp = time();
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Download object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//. load Download object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->filename = $ary['filename'];
		$this->hash = $ary['hash'];
		$this->status = $ary['status'];
		$this->timestamp = $ary['timestamp'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		if ('' == trim($this->filename)) { $report .= "No filename.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'sync';
		$dbSchema['model'] = 'Sync_Download';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'filename' => 'VARCHAR(255)',
			'hash' => 'VARCHAR(255)',
			'status' => 'VARCHAR(30)',
			'timestamp' => 'VARCHAR(33)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will not be kept for any field
		$dbSchema['nodiff'] = array();

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'filename' => $this->filename,
			'hash' => $this->hash,
			'status' => $this->status,
			'timestamp' => $this->timestamp,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
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

	//----------------------------------------------------------------------------------------------
	//.	clear old entries from the downloads table
	//----------------------------------------------------------------------------------------------

	function clearOldEntries() {
		global $db;
		$downloads = $db->loadRange('Sync_Download', '*', '');
		foreach($downloads as $row) {
			if (time() > ($row['timestamp'] + $this->maxAge)) {
				$db->delete($row['UID'], $this->dbSchema);
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a file is already in download queue
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if the file is already queued for download, otherwise false [bool]

	function inList($fileName) {
		global $db;
		$sql = "select * from Sync_Download where filename='" . $db->addMarkup($fileName) . "'";
		$result = $db->query($sql);
		if ($db->numRows($result) > 0) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if we're currently busy with too many files
	//----------------------------------------------------------------------------------------------
	//returns: true if this peer is already downloading maximum number of files, else false [bool]

	function maxDownloads() {
		global $db;

		//TODO: use dbCountRange
		$sql = "select UID from Sync_Download where status='searching'";
		$result = $db->query($sql);
		if ($db->numRows($result) > $this->numDownloads) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get next download
	//----------------------------------------------------------------------------------------------
	//returns: UID of next - random - download, false if none [string][bool]

	function getNextDownload() {
		global $db;

		$sql = "select * from Sync_Download where status='wait' order by rand()";
		$result = $db->query($sql);
		if ($db->numRows($result) > 0) {
			$row = $db->fetchAssoc($result);
			$row = $db->rmArray($row);
			return $row['UID'];

		} else { return false; }
	}
	
}
?>
