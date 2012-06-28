<?

//--------------------------------------------------------------------------------------------------
//*	object to represent local sessions of logged in users
//--------------------------------------------------------------------------------------------------
//+	this is used by features such as the chat, which need to know whether a user is logged in and 
//+	to which peer.

class Users_Session {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $user = '';			//_	user this session belongs to [string]
	var $role = '';			//_	role of this user [string]
	var $debug = false;		//_	toggles debug mode (yes|no) [string]

	var $UID;				//_ UID of this session [string]
	var $status;			//_ (active|inactive) [string]
	var $serverUID;			//_ varchar(255) [string]
	var $serverName;		//_ varchar(255) [string]
	var $serverUrl;			//_ varchar(255) [string]
	var $createdOn;			//_ login time (datetime) [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ last seen (datetime) [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared;			//_ share this object with other instances (yes|no) [string]

	//var $maxAge = 300;	// maximum age of user login session, in seconds [int]
	var $maxAge = 10;		// development value [int]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Login object [string]

	function Users_Session($UID = '') {
		global $kapenta;
		global $db;
		global $registry;

		$this->dbSchema = $this->getDbSchema();					//	initialise table schema

		if ('' != $UID) { 
			//--------------------------------------------------------------------------------------
			//	load explicitly named session object
			//--------------------------------------------------------------------------------------
			$this->msg("Loading explicit session ($UID)");
			$this->load($UID);

		} else {
			//--------------------------------------------------------------------------------------
			//	no session named by UID, load from session superglobal
			//--------------------------------------------------------------------------------------
			$this->UID = $this->get('UID');
			$this->user = $this->get('user');
			$this->role = $this->get('role');
			$this->debug = $this->get('debug');

			if ('' == $this->UID) { $this->UID = $kapenta->createUID(); }
			if ('' == $this->user) { $this->user = 'public'; }
			if ('' == $this->role) { $this->role = 'public'; }
			if ('' == $this->debug) { $this->debug = ''; }

			$this->set('UID', $this->UID);
			$this->set('user', $this->user);
			$this->set('role', $this->role);
			$this->set('debug', $this->debug);

			$this->serverUID = $registry->get('p2p.server.uid');
			$this->serverName = $registry->get('p2p.server.name');
			$this->serverUrl = $registry->get('p2p.server.url');
			$this->status = 'active';
			$this->createdBy = $this->user;
			$this->createdOn = $kapenta->datetime();
			$this->editedBy = $this->user;
			$this->editedOn = $kapenta->datetime();
			$this->shared = 'no';

			//--------------------------------------------------------------------------------------
			// try to detect mobile browsers
			//--------------------------------------------------------------------------------------

			

			//--------------------------------------------------------------------------------------
			// load more information from stored session if non-public user
			//--------------------------------------------------------------------------------------
			if ('public' != $this->role) { $this->loadUser($this->user); }

			//--------------------------------------------------------------------------------------
			// create session for non-public user if one was not found
			//--------------------------------------------------------------------------------------
			if ((false == $this->loaded) && ('public' != $this->role)) {
				$report = $this->save();
				if ('' != $report) { echo $report; }
			}

			//--------------------------------------------------------------------------------------
			// update editedOn if more than 5 minutes old
			//--------------------------------------------------------------------------------------
			if (true == $this->loaded) { $this->updateLastSeen(); }
			
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
	//.	load a session by user UID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]

	function loadUser($userUID) {
		global $db;

		//echo "Attempting to load stored session for user: $userUID <br/>";

		$conditions = array();
		$conditions[] = "status='active'";
		$conditions[] = "createdBy='" . $db->addMarkup($userUID) . "'";
		$range = $db->loadRange('users_session', '*', $conditions);

		if (0 == count($range)) { return false; }

		foreach($range as $row) { $this->loadArray($row); }
		return true;			// unreachable state, remove?
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
		$this->user = $ary['createdBy'];
		$this->status = $ary['status'];
		$this->serverUID = $ary['serverUID'];
		$this->serverName = $ary['serverName'];
		$this->serverUrl = $ary['serverURL'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $db;

		// NB: not shared with other users via P2P (chat is more efficient)
		$this->shared = 'no';

		$report = $this->verify();
		if ('' != $report) { return $report; }

		$objArray = $this->toArray();
		$objArray['editedBy'] = $this->user;
		$objArray['editedOn'] = $kapenta->datetime();

		$check = $db->save($objArray, $this->dbSchema, false);
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
		if (strlen($this->user) < 5) { $verify .= "User UID not present.\n"; }
		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'users_session';
		$dbSchema['archive'] = 'no';			// do not keep revision history or deleted records

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'status' => 'VARCHAR(10)',
			'serverUID' => 'VARCHAR(255)',
			'serverName' => 'VARCHAR(255)',
			'serverURL' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'status' => '3',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'status',
			'serverUID',
			'serverName',
			'serverUrl',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy',
			'shared'
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
			'status' => $this->status,
			'serverUID' => $this->serverUID,
			'serverName' => $this->serverName,
			'serverURL' => $this->serverUrl,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared
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
			. $indent . "    <status>" . $this->status . "</status>\n"
			. $indent . "    <serverUID>" . $this->serverUID . "</serverUID>\n"
			. $indent . "    <serverName>" . $this->serverUID . "</serverName>\n"
			. $indent . "    <serverUrl>" . $this->serverUrl . "</serverUrl>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <shared>" . $this->shared . "</shared>\n"
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
	//.	update editedOn field if it has been more then 5 minutes
	//----------------------------------------------------------------------------------------------
	//returns: true if db session object was updated, false if not [bool]

	function updateLastSeen() {
		global $kapenta;
		if (false == $this->loaded) { return false; }
		$lastSeen = $kapenta->strtotime($this->editedOn);		//%	last recorded time [int]
		$limit = $kapenta->time() - $this->maxAge;				//%	generally five minutes ago [int]
		if ($limit > $lastSeen) { $this->save(); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	log the user out
	//----------------------------------------------------------------------------------------------
	//returns: true on succes, false on failure [bool]

	function logout() {
		global $kapenta;
		global $user;

		if ('public' == $this->role) { return false; }
		
		$this->set('user', 'public');
		$this->set('role', 'public');
		$this->set('UID', $kapenta->createUID());

		$this->status = 'closed';
		$check = $this->save();
		if ('' == $check) { return true; }
		return false;
	}
	
	//==============================================================================================
	//	non-persistent session stuff (not stored in database, not kept after session)
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	discover if a session variable exists
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a session variable [string]
	//returns: true if this key is defined for this session [bool]

	function has($key) {
		if (true == array_key_exists('ks_' . $key, $_SESSION)) { return true; }
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	set the value of a session variable
	//----------------------------------------------------------------------------------------------
	//arg: key - name of a session vairable [string]
	//arg: value - new value for session variable [string]

	function set($key, $value) {
		$_SESSION['ks_' . $key] = $value;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the value of a session variable
	//----------------------------------------------------------------------------------------------

	function get($key) {
		if (false == array_key_exists('ks_' . $key, $_SESSION)) { return ''; }
		return $_SESSION['ks_' . $key];
	}

	//----------------------------------------------------------------------------------------------
	//.	add a message to be displayed to user on next page view
	//----------------------------------------------------------------------------------------------
	//arg: message - message to user [string]
	//opt: icon - message icon [string]

	function msg($message, $icon = 'info') {
		global $theme;
		$block = $theme->loadBlock('modules/home/views/sessionmsg.block.php');
		$labels = array('msg' => $message, 'icon' => $icon);

		$messages = $this->get('messages');
		$count = (int)$this->get('msgcount');		

		$messages .= $theme->replaceLabels($labels, $block);
		$this->set('messages', $messages);
		$this->set('msgcount', $count + 1);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a message to be displayed to an admin on next page view
	//----------------------------------------------------------------------------------------------
	//arg: message - message to user [string]
	//opt: icon - message icon [string]

	function msgAdmin($message, $icon = 'info') {
		global $user;
		if (false == isset($user)) { return false; }
		if ('admin' != $user->role) { return false; }
		$this->msg($message, $icon);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get all messages as html
	//----------------------------------------------------------------------------------------------
	//return: html [string]

	function messagesToHtml() {
		global $registry;
		global $theme;

		$html = '';								//%	return value [string]

		$messages = $this->get('messages');
		$maxMessages = $registry->get('users.maxmessages');
		$count = (int)$this->get('msgcount');		

		if (0 == $count) { return $html; }

		if ($count > $maxMessages) {
			$html = $theme->tb($messages, $count . ' Notices', 'divSMessage', 'hide');

		} else {
			$html = $theme->tb($messages, $count . ' Notices', 'divSMessage', 'show');
		}

		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	clear message history
	//----------------------------------------------------------------------------------------------

	function clearMessages() {
		$this->set('messages', '');
		$this->set('msgcount', '0');
	}

	//----------------------------------------------------------------------------------------------
	//.	examine user agent and try to detect mobile browsers
	//----------------------------------------------------------------------------------------------
	//returns: true if mobile device suspected [string]

	function guessMobile() {
		return false;
	}

}

?>
