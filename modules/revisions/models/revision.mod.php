<?

//--------------------------------------------------------------------------------------------------
//*	Recods hostory of significant changes to objects on other modules.
//--------------------------------------------------------------------------------------------------
//+	content is stored as XML with base64 encoded values, eg:
//+
//+		<fields>
//+			<title>BASE64ENCODEDTITLE</title>
//+			<content>BASE64ENCODEDCONTENT</content>
//+		</fields>
//+
//+	TODO: undelete mechanism - record is restored and the Revisions_Deleted object has its
//+	refUID field set to 'restored' and content set to the UID.  Peers, on reciving this
//+	updated Revisions_Deleted object should the re-sync the undeleted object, as they would for
//+ a new one.


class Revisions_Revision {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*_* [string]
	var $content;			//_ text [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	var $fields;			//_	associative [array]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Revision object [string]

	function Revisions_Revision($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Revision object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//. load Revision object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->content = $ary['content'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		
		$this->fields = $this->expandFields($this->content);
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
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
		$dbSchema['module'] = 'revisions';
		$dbSchema['model'] = 'Revisions_Revision';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'TEXT',
			'refModel' => 'TEXT',
			'refUID' => 'VARCHAR(33)',
			'content' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'content' );

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
			'content' => $this->collapseFields($this->fields),
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
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

		$xml = $indent . "<kobject type='Revisions_Revision'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <content>" . $this->content . "</content>\n"
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
		global $user, $utils;
		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('revisions', 'Revisions_Revision', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Revisions/showrevision/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('revisions', 'Revisions_Revision', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Revisions/editrevision/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('revisions', 'Revisions_Revision', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Revisions/delrevision/' . $ext['UID'];
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
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
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

}

?>
