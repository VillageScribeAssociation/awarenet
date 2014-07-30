<?

	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');

//--------------------------------------------------------------------------------------------------
//*	Record of an awareNet instance in this network.
//--------------------------------------------------------------------------------------------------

class Chatserver_Peer {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $peerUID;			//_ ref:p2p_peer [string]
	var $name;				//_ title [string]
	var $url;				//_ varchar(255) [string]
	var $pubkey;			//_ plaintext [string]
	var $shared;			//_ varchar(10) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]

	var $sessions;			//_	[object:Chatserver_Sessions]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Peer object [string]
	//opt: isPeerUID - set true to load be peerUID rather than object UID [bool]

	function Chatserver_Peer($UID = '', $isPeerUID = false) {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		$this->sessions = new Chatserver_Sessions('', false);

		if ('' != $UID) {
			if (false == $isPeerUID) { $this->load($UID); } 	// try load an object by UID
			if (true == $isPeerUID) { $this->loadPeer($UID); } 	// try load an object by peerUID
		}
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
			$this->shared = 'no';
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Peer object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given peerUID (ref:p2p_peer)
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of a Peer object [string]
	//returns: true on success, false on failure [bool]

	function loadPeer($peerUID) {
		global $db;
		$conditions = array("peerUID='" . $db->addMarkup($peerUID) . "'");
		$range = $db->loadRange('chatserver_peer', '*', $conditions);
		foreach ($range as $item) { return $this->loadArray($item); }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load Peer object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->peerUID = $ary['peerUID'];
		$this->name = $ary['name'];
		$this->url = $ary['url'];
		$this->pubkey = $ary['pubkey'];
		$this->shared = $ary['shared'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];

		$this->sessions->peerUID = $this->peerUID;
		$this->sessions->load();

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
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'chatserver';
		$dbSchema['model'] = 'chatserver_peer';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'peerUID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'url' => 'VARCHAR(255)',
			'pubkey' => 'MEDIUMTEXT',
			'shared' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'peerUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'name',
			'url',
			'pubkey',
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
			'peerUID' => $this->peerUID,
			'name' => $this->name,
			'url' => $this->url,
			'pubkey' => $this->pubkey,
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
		if (true == $user->authHas('chatserver', 'chatserver_peer', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%chatserver/showpeer/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_peer', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%chatserver/editpeer/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_peer', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%chatserver/delpeer/' . $ext['UID'];
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

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	sl - hash of all sessions local to this peer
	//----------------------------------------------------------------------------------------------

	function sl() {
		return $this->sessions->sl();
	}

}

?>
