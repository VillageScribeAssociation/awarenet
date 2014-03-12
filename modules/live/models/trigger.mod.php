<?

//--------------------------------------------------------------------------------------------------
//*	Event triggers for AJAX clients to receive updates.
//--------------------------------------------------------------------------------------------------

class Live_Trigger {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $pageUID;			//_ ref:*_* [string]
	var $module;			//_ varchar(100) [string]
	var $channel;			//_ varchar(255) [string]
	var $block;				//_ text [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared = 'no';		//_ not sharing with other peers [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Trigger object [string]

	function Live_Trigger($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Trigger object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Trigger object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->pageUID = $ary['pageUID'];
		$this->module = $ary['module'];
		$this->channel = $ary['channel'];
		$this->block = $ary['block'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
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
		$dbSchema['model'] = 'live_trigger';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'pageUID' => 'VARCHAR(33)',
			'module' => 'VARCHAR(100)',
			'channel' => 'VARCHAR(50)',
			'block' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'CHAR(3)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'pageUID' => '10',
			'module' => '10',
			'channel' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'pageUID',
			'module',
			'channel',
			'block',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy');

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
			'module' => $this->module,
			'channel' => $this->channel,
			'block' => $this->block,
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

		$xml = $indent . "<kobject type='live_trigger'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <pageUID>" . $this->pageUID . "</pageUID>\n"
			. $indent . "    <module>" . $this->module . "</module>\n"
			. $indent . "    <channel>" . $this->channel . "</channel>\n"
			. $indent . "    <block>" . $this->block . "</block>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <shared>no</shared>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $kapenta;
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
		if (true == $kapenta->user->authHas('live', 'live_trigger', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Live/showtrigger/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $kapenta->user->authHas('live', 'live_trigger', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Live/edittrigger/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $kapenta->user->authHas('live', 'live_trigger', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Live/deltrigger/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. send message to AJAX client when trigger is pulled
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function send() {
		$model = new Live_Mailbox($this->pageUID, true);

		if (false == $model->loaded) {
			// orphan trigger
			$this->delete();
			return false;
		}

		$message = 'block:' . base64_encode($this->block) . "\n";
		$model->messages .= $message;
		$report = $model->save();

		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

}

?>
