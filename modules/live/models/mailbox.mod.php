<?

//--------------------------------------------------------------------------------------------------
//*	Stores messages for AJAX clients.
//--------------------------------------------------------------------------------------------------

class Live_Mailbox {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $pageUID;			//_ ref:*_* [string]
	var $userUID;			//_ ref:Users_User [string]
	var $messages;			//_ plaintext [string]
	var $lastChecked0;		//_ timestamp (int) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared = 'no';		//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Mailbox object [string]
	//opt: isPage - set to true if this is a page UID and not a mailbox UID [bool]

	function Live_Mailbox($UID = '', $isPage = false) {
		global $kapenta;
		global $db;

		$this->dbSchema = $this->getDbSchema();				// initialise table schema

		if ('' != $UID) { 									// try load an object from the database
			if (false == $isPage) { $this->load($UID); }	// ... UID of a Live_Mailbox object
			else { $this->loadPage($UID); }					// ... UID of a page in the browser
		}

		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->lastChecked = $kapenta->time();			// when mailbox was last accessed
			$this->shared = 'no';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Mailbox object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. 
	//----------------------------------------------------------------------------------------------
	//arg: pageUID - UID of a page in the browser (Javascript message pump object)
	//returns: true on success, false on failure [bool]

	function loadPage($pageUID) {
		global $db;
		$conditions = array("pageUID='" . $db->addMarkup($pageUID) . "'");
		$range = $db->loadRange('live_mailbox', '*', $conditions);
		if (0 == count($range)) { return false; }
		foreach($range as $row) { $this->loadArray($row); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. load Mailbox object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->pageUID = $ary['pageUID'];
		$this->userUID = $ary['userUID'];
		$this->messages = $ary['messages'];
		$this->lastChecked = $ary['lastChecked'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = 'no';
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
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'live';
		$dbSchema['model'] = 'live_mailbox';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'pageUID' => 'VARCHAR(33)',
			'userUID' => 'VARCHAR(33)',
			'messages' => 'MEDIUMTEXT',
			'lastChecked' => 'BIGINT(20)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'pageUID' => '10',
			'userUID' => '10',
			'lastChecked' => '',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'pageUID',
			'userUID',
			'messages',
			'lastChecked' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'pageUID' => $this->pageUID,
			'userUID' => $this->userUID,
			'messages' => $this->messages,
			'lastChecked' => $this->lastChecked,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => 'no'
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='live_mailbox'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <pageUID>" . $this->pageUID . "</pageUID>\n"
			. $indent . "    <userUID>" . $this->userUID . "</userUID>\n"
			. $indent . "    <messages>" . $this->messages . "</messages>\n"
			. $indent . "    <lastChecked>" . $this->lastChecked . "</lastChecked>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		global $utils;
		global $theme;

		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('live', 'live_mailbox', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Live/showmailbox/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('live', 'live_mailbox', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Live/editmailbox/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('live', 'live_mailbox', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Live/delmailbox/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do

		// clear any triggers belonging to this page
		$sql = "delete from live_trigger where pageUID='" . $db->addMarkup($this->pageUID) . "'";
		$db->query($sql);

		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

}

?>
