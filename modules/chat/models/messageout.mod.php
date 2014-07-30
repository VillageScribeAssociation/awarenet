<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');

//--------------------------------------------------------------------------------------------------
//*	An outgoing chat message.
//--------------------------------------------------------------------------------------------------

class Chat_MessageOut {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $fromUser;			//_ uid [string]
	var $toRoom;			//_ uid [string]
	var $toUser;			//_ uid [string]
	var $message;			//_ text [string]
	var $sent;				//_ varchar(10) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ ref:users_user [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a MessageOut object [string]

	function Chat_MessageOut($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }		// try load an object from the database
		if (false == $this->loaded) {				// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
			$this->shared = 'no';					// not shared over P2P system 
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a MessageOut object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load MessageOut object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->fromUser = $ary['fromUser'];
		$this->toRoom = $ary['toRoom'];
		$this->toUser = $ary['toUser'];
		$this->message = $ary['message'];
		$this->sent = $ary['sent'];
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
		$dbSchema['module'] = 'chat';
		$dbSchema['model'] = 'chat_messageout';
		$dbSchema['archive'] = 'no';				// do not keep revision history

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'fromUser' => 'VARCHAR(30)',
			'toRoom' => 'VARCHAR(30)',
			'toUser' => 'VARCHAR(30)',
			'message' => 'TEXT',
			'sent' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'fromUser' => '10',
			'toRoom' => '10',
			'toUser' => '10',
			'sent' => '2',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'fromUser',
			'toRoom',
			'toUser',
			'message',
			'sent'
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
			'fromUser' => $this->fromUser,
			'toRoom' => $this->toRoom,
			'toUser' => $this->toUser,
			'message' => $this->message,
			'sent' => $this->sent,
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
		if (true == $user->authHas('chat', 'chat_messageout', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%chat/showmessageout/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('chat', 'chat_messageout', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%chat/editmessageout/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('chat', 'chat_messageout', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%chat/delmessageout/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize as XML for chat server
	//----------------------------------------------------------------------------------------------
	
	function toXml() {
		$xml = ''
		 . "<message>\n"
		 . "\t<uid>" . $this->UID . "</uid>\n"
		 . "\t<room>" . $this->toRoom . "</room>\n"
		 . "\t<fromuser>" . $this->fromUser . "</fromuser>\n"
		 . "\t<touser>" . $this->toUser . "</touser>\n"
		 . "\t<message64>" . base64_encode($this->message) . "</message64>\n"
		 . "</message>\n";

		return $xml;
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
	//.	syncronous send (directly to chat server, not using chat client)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]
	//;	this is kept for test purposes, but deprecated in favor of collection obejct

	function send() {
		$xml = $this->toXml();
		echo "<div class='chatmessageblack'>";
		echo "<b>Request:</b><br/><textarea rows='10' cols='80'>$xml</textarea><br/>\n";

		$io = new Chat_IO();
		$response = $io->send('send', '', "<mn>\n" . $xml . "</mn>\n");

		echo "<b>Response:</b><br/><textarea rows='10' cols='80'>$response</textarea><br/>\n";
		echo "</div>";

		if (false == strpos($response, "<ok/>")) { return false; }
		return true;
	}

}

?>
