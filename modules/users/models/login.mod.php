<?

//--------------------------------------------------------------------------------------------------
//*	object to represent loghged in user sessions
//--------------------------------------------------------------------------------------------------
//+	this is used by features such as the chat, which need to know whether a user is logged in and 
//+	to which peer.

class Users_Login {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $userUID;			//_ ref:Users_User [string]
	var $serverUID;			//_ varchar(255) [string]
	var $serverName;		//_ varchar(255) [string]
	var $logintime;			//_ datetime [string]
	var $lastseen;			//_ datetime [string]
	var $status;			//_ datetime [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared;			//_ share this object with other instances (yes|no) [string]
	var $revision;			//_ bigint [string]

	var $maxAge = 300;		// maximum age of user login session, in seconds [int]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Login object [string]
	//opt: isUser - set to UID is of a Users_User object, not a Users_Login object [bool]

	function Users_Login($UID = '', $isUser = false) {
		global $kapenta;
		global $db;

		$this->dbSchema = $this->getDbSchema();				// initialise table schema

		if ('' != $UID) { 
			if (false == $isUser) { $this->load($UID);	}	// try load Users_Login object
			else { $this->loadUser($UID); }					// try to load Users_User object
		}

		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->serverUID = $kapenta->serverPath;		// ...user was here
			$this->logintime = $db->datetime();				// ...at this time
			$this->lastseen = $db->datetime();				// ...
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Login object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a login session object by user UID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]

	function loadUser($userUID) {
		global $db;

		$conditions = array("userUID='" . $db->addMarkup($userUID) . "'");
		$range = $db->loadRange('users_login', '*', $conditions);

		//$sql = "select * from users_login where userUID='" . $db->addMarkup($userUID) . "'";

		if (false === $range) { return false; }
		if (0 == count($range)) { return false; }

		foreach($range as $row) {
			$this->loadArray($row);
			return true;
		}

		return false;		// unreachable state, remove?
	}

	//----------------------------------------------------------------------------------------------
	//. load Login object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->userUID = $ary['userUID'];
		$this->serverUID = $ary['serverUID'];
		$this->logintime = $ary['logintime'];
		$this->lastseen = $ary['lastseen'];
		$this->status = $ary['status'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->revision = $ary['revision'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;
		global $session;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		if (strlen($this->UID) < 5) { $verify .= "UID not present.\n"; }
		if (strlen($this->userUID) < 5) { $verify .= "User UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'users_login';
		$dbSchema['archive'] = 'no';			// do not keep revision history or deleted records

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'userUID' => 'VARCHAR(33)',
			'serverUID' => 'VARCHAR(255)',
			'serverName' => 'VARCHAR(255)',
			'logintime' => 'DATETIME',
			'lastseen' => 'DATETIME',
			'status' => 'DATETIME',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)',
			'revision' => 'BIGINT(20)' 
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'userUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'userUID',
			'serverUID',
			'serverName',
			'logintime',
			'lastseen',
			'status',
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
			'userUID' => $this->userUID,
			'serverUID' => $this->serverUID,
			'serverName' => $this->serverName,
			'logintime' => $this->logintime,
			'lastseen' => $this->lastseen,
			'status' => $this->status,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared,
			'revision' => $this->revision
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
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='users_login'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <userUID>" . $this->userUID . "</userUID>\n"
			. $indent . "    <serverUID>" . $this->serverUID . "</serverUID>\n"
			. $indent . "    <serverName>" . $this->serverUID . "</serverName>\n"
			. $indent . "    <logintime>" . $this->logintime . "</logintime>\n"
			. $indent . "    <lastseen>" . $this->lastseen . "</lastseen>\n"
			. $indent . "    <status>" . $this->status . "</status>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <shared>" . $this->shared . "</shared>\n"
			. $indent . "    <revision>" . $this->revision . "</revision>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current user login session object
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $db;
		$db->delete($this->UID, $this->dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//.	update lastseen to current time
	//----------------------------------------------------------------------------------------------

	function updateLastSeen($userUID) {
		global $db;
		$db->updateQuiet('users_login', $this->UID, 'lastseen', $db->datetime());
	}
	
}
?>
