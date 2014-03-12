<?

//--------------------------------------------------------------------------------------------------
//*	Stores items deleted from other tables.
//--------------------------------------------------------------------------------------------------

class Revisions_Deleted {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $data;						//_	currently loaded database record [array]
	var $dbSchema;					//_	database table definition [array]
	var $loaded = false;			//_	set to true when an object has been loaded [bool]

	var $UID;						//_ unique identifier of this object [string]
	var $refModule;					//_ module to which the deleted item belonged [string]
	var $refModel;					//_ type of deleted object [string]
	var $refUID;					//_ unique identifier of deleted object [string]
	var $owner;						//_	unique identifier of original owner [string]
	var $content;					//_ member variables of deleted object (xml) [string]
	var $status;					//_ deleted, restored or purged [string]
	var $createdOn;					//_ datetime [string]
	var $createdBy;					//_ ref:Users_User [string]
	var $editedOn;					//_ datetime [string]
	var $editedBy;					//_ ref:Users_User [string]
	var $shared;					//_ share with other instances via p2p module/network [string]

	var $fields;					//_	associative [array]
	var $fieldsLoaded = false;		//_	set to true when fields have been expanded from XML [bool]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Deleted object [string]

	function Revisions_Deleted($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->status = 'deleted';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Deleted object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Deleted object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->owner = $ary['owner'];
		$this->content = $ary['content'];
		$this->status = $ary['status'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];

		$this->fields = $this->expandFields($this->content);
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
		$dbSchema['module'] = 'revisions';
		$dbSchema['model'] = 'revisions_deleted';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'TEXT',
			'refModel' => 'TEXT',
			'refUID' => 'VARCHAR(33)',
			'owner' => 'VARCHAR(33)',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'owner' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array('content');

		return $dbSchema;		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'owner' => $this->owner,
			'content' => $this->collapseFields($this->fields),
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
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='revisions_deleted'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <owner>" . $this->owner . "</owner>\n"
			. $indent . "    <content>" . $this->content . "</content>\n"
			. $indent . "    <status>" . $this->status . "</status>\n"
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
		global $kapenta;
		global $utils;

		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $kapenta->user->authHas('revisions', 'revisions_deleted', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Revisions/showdeleted/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $kapenta->user->authHas('revisions', 'revisions_deleted', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Revisions/editdeleted/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $kapenta->user->authHas('revisions', 'revisions_deleted', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Revisions/deldeleted/' . $ext['UID'];
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
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	restore to live database / undelete
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]	

	function restore() {
		global $kapenta;
		global $session;

		if (false == $this->fieldsLoaded) { $this->fields = $this->expandFields($this->content); }
		if (false == $this->fieldsLoaded) { return false; }
		if (false == $kapenta->db->tableExists($this->refModel)) { return false; }

		$kapenta->session->msg("restoring deleted object " . $this->refModel . '::' . $this->refUID);

		//------------------------------------------------------------------------------------------
		//	mark for restoration
		//------------------------------------------------------------------------------------------
		$this->status = 'restore';
		$report = $this->save();
		if ('' != $report) { return false; }
	
		//------------------------------------------------------------------------------------------
		//	copy back to original table
		//------------------------------------------------------------------------------------------
		$dbSchema = $kapenta->db->getSchema($this->refModel);
		$check = $kapenta->db->save($this->fields, $dbSchema, false);
		return $check;
	}

	//==============================================================================================
	//	fields
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	convert fields XML to associative array (typically onLoad)
	//----------------------------------------------------------------------------------------------
	//arg: xml - has 'fields' root element, values base64 encoded [string]
	//returns: associative [array]

	function expandFields($xml) {
		if ('' == $xml) { return array(); }
		$doc = new KXmlDocument($xml);
		$fields = $doc->getChildren2d(1);						//% 1 is always root node [array]
		foreach($fields as $key => $value) { $fields[$key] = base64_decode($value); }
		$this->fieldsLoaded = true;
		return $fields;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert fields array to XML
	//----------------------------------------------------------------------------------------------
	//arg: xml - has 'fields' root element, values base64 encoded [string]
	//returns: associative [array]

	function collapseFields($fields) {
		global $utils;
		foreach($fields as $name => $value) { $fields[$name] = base64_encode($value); }
		$xml = $utils->arrayToXml2d('fields', $fields);
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if the deleted object has a given property / field
	//----------------------------------------------------------------------------------------------
	//arg: name - name of a field / property [string]
	//returns: true if property is present, false if not [bool]
	//TODO: make mroe efficient

	function hasProperty($name) {
		if (false == $this->fieldsLoaded) { $this->fields = $this->expandFields($this->content); }
		if (false == $this->fieldsLoaded) { return false; }
		foreach($this->fields as $key => $value) { if ($key == $name) { return true; } }
		return false;
	}

}

?>
