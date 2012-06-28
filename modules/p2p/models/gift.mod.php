<?

//--------------------------------------------------------------------------------------------------
//*	Peers gift each other with new datam gossip protocol.
//--------------------------------------------------------------------------------------------------

class P2P_Gift {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $peer;				//_ ref:P2P_Peer [string]
	var $type;				//_ (object|file) varchar(10) [string]
	var $refModel;			//_ varchar(100) [string]
	var $refUID;			//_ varchar(50) [string]
	var $fileName;			//_ relative to installPath varchar(255) [string]
	var $hash;				//_ varchar(50) [string]
	var $updated;			//_ datetime [string]
	var $status;			//_ varchar(10) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared = 'no';		//_ these objects are not shared with other peers varchar(10) [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Gift object [string]

	function P2P_Gift($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Gift object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Gift object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->peer = $ary['peer'];
		$this->type = $ary['type'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->fileName = $ary['fileName'];
		$this->hash = $ary['hash'];
		$this->updated = $ary['updated'];
		$this->status = $ary['status'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		//$this->shared = $ary['shared'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $db;
		global $session;

		$report = '';							//% return value [string]

		//------------------------------------------------------------------------------------------
		//	check properties
		//------------------------------------------------------------------------------------------

		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		if ('' == $this->refUID) { $report .= "No refUID.<br/>\n"; }
		if ('' == $this->refModel) { $report .= "No refModel.<br/>\n"; }
		if ('' == $this->status) { $report .= "Status not set.<br/>\n"; }
		if ('' == $this->peer) { $report .= "Peer not specified.<br/>\n"; }

		if ('file' == $this->type) {
			if ('' == $this->fileName) { $report .= "No filename."; }
			if ('' == $this->hash) { $report .= "No file hash."; }
		}

		if ('' == $this->updated) { $report .= "Update time not set."; }
		if ('0000-00-00 00:00:00' == $this->updated) { $report .= "Update time not set."; }

		if ('p2p_gift' == $this->refModel) { $report .= "Tried to save share P2P_Gift object."; }

		//------------------------------------------------------------------------------------------
		//	check for temporary and disallowed tables
		//------------------------------------------------------------------------------------------

		if ('tmp_' === substr($this->refModel, 0, 4)) {
			$report .= "Tried to share item from temporary table.";
		}

		//------------------------------------------------------------------------------------------
		//	check database for duplicates
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "peer='" . $db->addMarkup($this->peer) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($this->refUID) . "'";
		$conditions[] = "refModel='" . $db->addMarkup($this->refModel) . "'";
		$conditions[] = "type='" . $db->addMarkup($this->type) . "'";
		$range = $db->loadRange('p2p_gift', '*', $conditions);

		if (count($range) > 1) {
			foreach($range as $item) {
				//	Remove duplicate			
				$check = $db->delete($this->UID, $this->dbSchema);

				if (true == $check) {
					$msg = 'Removed duplicate gift entry for ' . $this->refModel . '::' . $this->refUID;
					$session->msgAdmin($msg);
				} else {
					$msg = 'Error: duplicate gift entry for ' . $this->refModel . '::' . $this->refUID;
					$session->msgAdmin($msg);
				}

				//	Overwrite existing record
				$this->UID = $item['UID'];
			}
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'p2p';
		$dbSchema['model'] = 'p2p_gift';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'peer' => 'VARCHAR(30)',
			'type' => 'VARCHAR(10)',
			'refModel' => 'VARCHAR(100)',
			'refUID' => 'VARCHAR(50)',
			'fileName' => 'VARCHAR(255)',
			'hash' => 'VARCHAR(50)',
			'updated' => 'DATETIME',
			'status' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(10)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'peer' => '10',
			'type' => '5',
			'refModel' => '10',
			'refUID' => '10',
			'status' => '5',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'peer',
			'type',
			'model',
			'giftUID',
			'fileName',
			'hash',
			'updated',
			'status',
			'shared'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'peer' => $this->peer,
			'type' => $this->type,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'fileName' => $this->fileName,
			'hash' => $this->hash,
			'updated' => $this->updated,
			'status' => $this->status,
			'shared' => $this->shared,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
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
		if (true == $user->authHas('p2p', 'p2p_gift', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%p2p/showgift/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_gift', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%p2p/editgift/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_gift', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%p2p/delgift/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
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
