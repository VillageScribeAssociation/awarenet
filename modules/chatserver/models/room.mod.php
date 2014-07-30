<?

	require_once($kapenta->installPath . 'modules/chatserver/models/memberships.set.php');

//--------------------------------------------------------------------------------------------------
//*	Object to hold members and messages together.
//--------------------------------------------------------------------------------------------------

class Chatserver_Room {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title [string]
	var $description;		//_ wyswyg [string]
	var $memberCount;		//_ bigint [string]
	var $emptyOn;			//_ datetime [string]
	var $status;			//_ varchar(10) [string]
	var $revision;			//_ bigint [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ shared [string]

	var $memberships;		//_	associates users with this room [object:Chatserver_Memberships]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Room object [string]

	function Chatserver_Room($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		$this->memberships = new Chatserver_Memberships($UID); 

		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
			$this->shared = 'no';
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Room object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Room object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->description = $ary['description'];
		$this->memberCount = $ary['memberCount'];
		$this->emptyOn = $ary['emptyOn'];
		$this->status = $ary['status'];
		$this->revision = $ary['revision'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = 'no';

		$this->memberships->roomUID = $this->UID;
		$this->memberships->load();						//TODO: lazy initialization

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
		$dbSchema['model'] = 'chatserver_room';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'memberCount' => 'TEXT',
			'emptyOn' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'revision' => 'BIGINT(15)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'title' => '10',
			'memberCount' => '10',
			'status' => '3',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'title',
			'description',
			'memberCount',
			'emptyOn',
			'status',
			'revision'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to XML
	//----------------------------------------------------------------------------------------------

	function toXml() {
		$xml = ''
		 . "<chatroom>\n"
		 . "\t<uid>" . $this->UID . "</uid>\n"
		 . "\t<title64>" . base64_encode($this->title) . "</title64>\n"
		 . "\t<description64>" . base64_encode($this->description) . "</description64>\n"
		 . "\t<emptyon>" . $this->emptyOn . "</emptyon>\n"
		 . "\t<createdby>" . $this->createdBy . "</createdby>\n"
		 . "\t<createdon>" . $this->createdOn . "</createdon>\n"
		 . "\t<editedby>" . $this->editedBy . "</editedby>\n"
		 . "\t<editedon>" . $this->editedOn . "</editedon>\n"
		 . "</chatroom>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'title' => $this->title,
			'description' => $this->description,
			'memberCount' => $this->memberCount,
			'emptyOn' => $this->emptyOn,
			'status' => $this->status,
			'revision' => $this->revision,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => 'no'
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
		if (true == $user->authHas('chatserver', 'chatserver_room', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%chatserver/showroom/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_room', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%chatserver/editroom/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_room', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%chatserver/delroom/' . $ext['UID'];
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

	//----------------------------------------------------------------------------------------------
	//.	rh - room hash
	//----------------------------------------------------------------------------------------------
	//arg: roomUID - UID of a Chat_Room object [string]
	//returns: room hash, or empty string on failure [string]

	function rh() {
		if (false == $this->loaded) { return ''; }

		$txt = '';

		$txt = $this->UID . '|' . $this->title . '|' . $this->description;
		$hash = sha1($txt);
		//$this->set('rh-' . $roomUID, $hash);		//TODO: cache this
		return $hash;
	}

}

?>
