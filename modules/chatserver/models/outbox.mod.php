<?

//--------------------------------------------------------------------------------------------------
//*	A chat message to be delivered to a user.
//--------------------------------------------------------------------------------------------------

class Chatserver_Outbox {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $room;				//_ ref:Chatserver_Room [string]
	var $fromUser;			//_ ref:Users_User [string]
	var $toUser;			//_ ref:Users_User [string]
	var $message;			//_ plaintext [string]
	var $delivered;			//_ varchar(10) [string]
	var $peer;				//_	hint, used for indexing [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ shared [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Outbox object [string]

	function Chatserver_Outbox($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->shared = 'no';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Outbox object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Outbox object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->room = $ary['room'];
		$this->fromUser = $ary['fromUser'];
		$this->toUser = $ary['toUser'];
		$this->message = $ary['message'];
		$this->delivered = $ary['delivered'];
		$this->peer = $ary['peer'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
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
		$dbSchema['model'] = 'chatserver_outbox';

		//------------------------------------------------------------------------------------------
		//table columns
		//------------------------------------------------------------------------------------------
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'room' => 'VARCHAR(30)',
			'fromUser' => 'VARCHAR(30)',
			'toUser' => 'VARCHAR(30)',
			'message' => 'MEDIUMTEXT',
			'delivered' => 'VARCHAR(10)',
			'peer' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//------------------------------------------------------------------------------------------
		//these fields will be indexed
		//------------------------------------------------------------------------------------------
		$dbSchema['indices'] = array(
			'UID' => '10',
			'room' => '10',
			'toUser' => '10',
			'delivered' => '3',
			'peer' => '4',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1'
		);

		//------------------------------------------------------------------------------------------
		//no revision history will be kept for any of these fields
		//------------------------------------------------------------------------------------------
		$dbSchema['nodiff'] = array(
			'room',
			'fromUser',
			'toUser',
			'message',
			'delivered',
			'peer',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy',
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
			'room' => $this->room,
			'fromUser' => $this->fromUser,
			'toUser' => $this->toUser,
			'message' => $this->message,
			'delivered' => $this->delivered,
			'peer' => $this->peer,
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
		if (true == $user->authHas('chatserver', 'chatserver_outbox', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%chatserver/showoutbox/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_outbox', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%chatserver/editoutbox/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('chatserver', 'chatserver_outbox', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%chatserver/deloutbox/' . $ext['UID'];
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
