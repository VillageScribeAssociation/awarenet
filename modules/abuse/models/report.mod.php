<?

//--------------------------------------------------------------------------------------------------
//*	object to represent abuse reports
//--------------------------------------------------------------------------------------------------

class Abuse_Report {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*_* [string]
	var $comment;			//_ plaintext [string]
	var $notes;				//_ plaintext [string]
	var $status;			//_ varchar(50) [string]
	var $title;				//_ title [string]
	var $fromurl;			//_ varchar(255) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Report object [string]

	function Abuse_Report($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				        // initialise table schema
		if ('' != $UID) { $this->load($UID); }				        // try load an object from the database
		if (false == $this->loaded) {						        // check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					        // initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Report object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if (false == $objary) { return false; }
		if (false == $this->loadArray($objary)) { return false; }
		return true;
	}


	//----------------------------------------------------------------------------------------------
	//. load Report object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		//if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->comment = $ary['comment'];
		$this->notes = $ary['notes'];
		$this->status = $ary['status'];
		$this->title = $ary['title'];
		$this->fromurl = $ary['fromurl'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
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

		$this->comment = str_replace('widthcontent', 'widthindent', $this->comment);
		$this->comment = str_replace('widtheditor', 'widthindent', $this->comment);
		$this->comment = str_replace('width570', 'widthindent', $this->comment);

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'abuse';
		$dbSchema['model'] = 'abuse_report';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'TEXT',
			'refModel' => 'TEXT',
			'refUID' => 'VARCHAR(33)',
			'comment' => 'MEDIUMTEXT',
			'notes' => 'MEDIUMTEXT',
			'status' => 'VARCHAR(50)',
			'title' => 'VARCHAR(255)',
			'fromurl' => 'VARCHAR(255)',
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
			'comment',
			'notes',
			'status' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
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
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'comment' => $this->comment,
			'notes' => $this->notes,
			'status' => $this->status,
			'title' => $this->title,
			'fromurl' => $this->fromurl,
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

		$xml = $indent . "<kobject type='abuse_report'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <refModule>" . $this->refModule . "</refModule>\n"
			. $indent . "    <refModel>" . $this->refModel . "</refModel>\n"
			. $indent . "    <refUID>" . $this->refUID . "</refUID>\n"
			. $indent . "    <comment>" . $this->comment . "</comment>\n"
			. $indent . "    <notes>" . $this->notes . "</notes>\n"
			. $indent . "    <status>" . $this->status . "</status>\n"
			. $indent . "    <title>" . $this->title . "</title>\n"
			. $indent . "    <fromurl>" . $this->fromurl . "</fromurl>\n"
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

		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		$auth = false;
		if ('admin' == $user->role) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $auth) {
			$ext['viewUrl'] = '%%serverPath%%abuse/show/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if ('admin' == $user->role) {
			$ext['editUrl'] = '%%serverPath%%abuse/editreport/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
			$ext['delUrl'] = '%%serverPath%%abuse/delreport/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	...
		//------------------------------------------------------------------------------------------
		$ext['commentHtml'] = $utils->stripHtml($ext['comment']);
		$ext['commentHtml'] = str_replace("\n", "<br/>\n", $ext['commentHtml']);
		$ext['commentHtml'] = str_replace("&amp;", "&", $ext['commentHtml']);

		$ext['notesHtml'] = '';
		if ('' != $ext['notes']) {
			$ext['notesHtml'] = $ext['notes'];
			//$ext['notesHtml'] = str_replace("\n", "<br/>\n", $ext['notesHtml']);
		} else { $ext['notesHtml'] = '(none yet)'; }

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
	//. add an annotation
	//----------------------------------------------------------------------------------------------
	//arg:

	function annotate($userUID, $note) {
		global $user;
		global $kapenta;

		if ('' == trim($note)) { return false; }
		$this->notes .= "<!-- annotation -->\n"
			. "<b>" . $user->getNameLink() . "</b>"
			. " (added: " . $kapenta->db->datetime() . ")<br/>"
			. str_replace("\n", "<br/>\n", $note) . "<br/>"
			. ""
			. "<hr/>";

		//$session->msg('Added annotation to Abuse Report.', 'ok');
		$_SESSION['sMessage'] .= "Added annotation to Abuse Report " . $this->UID . "<br/>\n";
		$this->save();
	}

}

?>
