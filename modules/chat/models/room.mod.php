<?
	
	require_once($kapenta->installPath . 'modules/chat/models/memberships.set.php');

//--------------------------------------------------------------------------------------------------
//*	Chat room
//--------------------------------------------------------------------------------------------------

class Chat_Room {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $title;				//_ title / room topic [string]
	var $description;		//_ wyswyg [string]
	var $memberCount;		//_ bigint [string]
	var $state;				//_	(local|global) [string]
	var $emptyOn;			//_ datetime [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_	shared with other peers via p2p module (yes|no) [string]

	var $memberships;		//_	room memberships [object:Chat_Memberships]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Room object [string]

	function Chat_Room($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();			//	initialise table schema
		$this->memberships = new Chat_Memberships();	//	initialize membership set

		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->state = 'local';
			$this->shared = 'no';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Room object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
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
		$this->state = $ary['state'];
		$this->emptyOn = $ary['emptyOn'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];

		$this->memberships->roomUID = $this->UID;
		$this->memberships->load();						//TODO: lazy initialization

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load from XML (as created by chatserver module to describe a global room)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on faiilure [bool]

	function loadXml($xml) {
		$xd = new KXmlDocument($xml);
		$entity = $xd->getEntity(1);			//%	get root entity [dict]
		if ('chatroom' != $entity['type']) { return false; }

		$objAry = $xd->getChildren2d();			//%	children of root node [dict]

		$this->UID = $objAry['uid'];
		$this->title = base64_decode($objAry['title64']);
		$this->description = base64_decode($objAry['description64']);
		$this->memberCount = '0';
		$this->state = 'global';
		$this->emptyOn = $objAry['emptyon'];
		$this->createdOn = $objAry['createdon'];
		$this->createdBy = $objAry['createdby'];
		$this->editedOn = $objAry['editedon'];
		$this->editedBy = $objAry['editedby'];
		$this->shared = 'no';

		return true;
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
		$dbSchema['module'] = 'chat';
		$dbSchema['model'] = 'chat_room';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'memberCount' => 'BIGINT(20)',
			'state' => 'VARCHAR(10)',
			'emptyOn' => 'DATETIME',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'title',
			'description',
			'memberCount',
			'emptyOn'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to XML for exporting to chat server
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment [string]

	function toXml($indent = '') {
		if (false == $this->loaded) { return ''; }

		$xml = ''
		 . $indent . "<room>\n"
		 . $indent . "\t<uid>" . $this->UID . "</uid>\n"
		 . $indent . "\t<title64>" . base64_encode($this->title) . "</title64>\n"
		 . $indent . "\t<description64>" . base64_encode($this->description) . "</description64>\n"
		 . $indent . "\t<createdon>" . $this->createdOn . "</createdon>\n"
		 . $indent . "\t<createdby>" . $this->createdBy . "</createdby>\n"
		 . $indent . "\t<editedon>" . $this->editedOn . "</editedon>\n"
		 . $indent . "\t<editedby>" . $this->editedBy . "</editedby>\n"
		 . $indent . "</room>\n";

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
			'state' => $this->state,
			'emptyOn' => $this->emptyOn,
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

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('chat', 'chat_room', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%chat/showroom/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
			$ext['titleLink'] = "<a href='" . $ext['viewUrl'] . "'>" . $ext['title'] . "</a>";
		}

		if (true == $user->authHas('chat', 'chat_room', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%chat/editroom/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('chat', 'chat_room', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%chat/delroom/' . $ext['UID'];
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
