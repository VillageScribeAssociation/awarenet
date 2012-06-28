<?

//--------------------------------------------------------------------------------------------------
//*	Record of another kapenta instance which we trust.
//--------------------------------------------------------------------------------------------------

class P2P_Peer {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ title [string]
	var $url;				//_ varchar(255) [string]
	var $firewalled;		//_ is this peer behind a firewall? (yes|) varchar(10) [string]
	var $pubkey;			//_ plaintext [string]
	var $status;			//_ varchar(30) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared = 'no';		//_ varchar(10) [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Peer object [string]

	function P2P_Peer($UID = '') {
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
	//arg: UID - UID of a Peer object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
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
		$this->name = $ary['name'];
		$this->url = $ary['url'];
		$this->firewalled = $ary['firewalled'];
		$this->pubkey = $ary['pubkey'];
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
		$dbSchema['module'] = 'p2p';
		$dbSchema['model'] = 'p2p_peer';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'url' => 'VARCHAR(255)',
			'firewalled' => 'VARCHAR(10)',
			'pubkey' => 'TEXT',
			'status' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(10)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'name' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'name',
			'url',
			'firewalled',
			'pubkey',
			'shared',
			'status'
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
			'name' => $this->name,
			'url' => $this->url,
			'firewalled' => $this->firewalled,
			'pubkey' => $this->pubkey,
			'status' => $this->status,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared
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
		$ext['testUrl'] = '';	$ext['testLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('p2p', 'p2p_peer', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%p2p/showpeer/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_peer', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%p2p/editpeer/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";

			$ext['testUrl'] = '%%serverPath%%p2p/testsend/' . $ext['UID'];
			$ext['testLink'] = "<a href='" . $ext['testUrl'] . "'>[ test ]</a>";

			$ext['scanUrl'] = '%%serverPath%%p2p/findgifts/' . $ext['UID'];
			$ext['scanLink'] = "<a href='" . $ext['scanUrl'] . "'>[ scan for gifts ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_peer', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%p2p/confirmdelete/UID_' . $ext['UID'];
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
	//	network IO
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	send a message to this peer
	//----------------------------------------------------------------------------------------------
	//returns: empty string on success, error message on failure [string]

	function sendMessage($action, $message) {
		global $registry;
		global $utils;

		$url = $this->url . 'p2p/' . $action . '/';				//%	interface to POST to [string]
		$report = ''; 											//%	return value [string]
		$signature = '';										//%	against own prv key [string]
		$prvkey = $registry->get('p2p.server.prvkey');			//%	this peer's signing key [string] 

		if ('' == $prvkey) { return 'No private key set in registry.'; }

		$prvkeyid = openssl_get_privatekey($prvkey);

		openssl_sign($message, $signature, $prvkeyid);		// compute using OPENSSL_ALGO_SHA1
		openssl_free_key($prvkeyid);						// free the key

		$postvars = array(
			'peer' => $registry->get('p2p.server.uid'),
			'message' => base64_encode($message),
			'signature' => base64_encode($signature)
		);

		$returns = $utils->curlPost($url, $postvars);
		
		if ('<ok/>' != $returns) { $report .= $returns; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	check a message sent by this peer, ensure signature matches data
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [string]

	function checkMessage($message, $signature) {
		$pubkeyid = openssl_get_publickey($this->pubkey);
		$check = openssl_verify($message, $signature, $pubkeyid);
		return $check;
	}

}

?>
