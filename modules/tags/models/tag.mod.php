<?

//--------------------------------------------------------------------------------------------------
//*	Central organising object
//--------------------------------------------------------------------------------------------------

class Tags_Tag {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ title [string]
	var $namelc;			//_ for searching [string]
	var $objectCount;		//_ bigint [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Tag object [string]
	//opt: byName - if true then load by name rather than by UID [bool]

	function Tags_Tag($UID = '', $byName = false) {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema

		if ('' != $UID) { 									// try load an object from the database
			if (false == $byName) { $this->load($UID); }		// ... by object UID
			if (true == $byName) { $this->loadByName($UID); }	// ... by tag name
		}

		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->name = 'New Tag ' . $this->UID;			// set default name
			$this->namels = strtolower($this->name);		// set default name
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Tag object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load a tag object by name
	//----------------------------------------------------------------------------------------------
	//arg: tagName - name of a tag, case insensitive [string]
	//returns: true on success, false on failure [bool]

	function loadByName($tagName) {
		global $db;

		$tagUID = $this->getTagUID($tagName);		//	this causes the tag object to become cached
		if (false == $tagUID) { return false; }		//	no such object
		$this->load($tagUID);						//	will load from db->cache
		return $this->loaded;
	}

	//----------------------------------------------------------------------------------------------
	//. load Tag object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		//if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->namelc = strtolower(trim($ary['name']));
		$this->objectCount = $ary['objectCount'];
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

		// check that a tag name was given
		$this->namelc = strtolower(trim($this->name));
		if ('' == $this->namelc) { $report .= "Tag is blank.<br/>\n"; }

		// check that this tag does not already exist
		$tagUID = $this->getTagUID($this->name);
		if ((false != $tagUID) && ($tagUID != $this->UID)) 
			{ $report .= "Tag already exists under a different UID.<br/>\n"; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'tags';
		$dbSchema['model'] = 'tags_tag';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'name' => 'VARCHAR(255)',
			'namelc' => 'VARCHAR(255)',
			'objectCount' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'name' => '10',
			'objectCount' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'name',
			'objectCount' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'name' => $this->name,
			'namelc' => strtolower($this->name),
			'objectCount' => $this->objectCount,
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

		$xml = $indent . "<kobject type='tags_tag'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <name>" . $this->name . "</name>\n"
			. $indent . "    <namelc>" . $this->namelc . "</namelc>\n"
			. $indent . "    <objectCount>" . $this->objectCount . "</objectCount>\n"
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
		global $user, $utils, $theme;
		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('tags', 'tags_tag', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Tags/showtag/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('tags', 'tags_tag', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Tags/edittag/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('tags', 'tags_tag', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Tags/deltag/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. get UID of a Tags_Tag object given its name
	//----------------------------------------------------------------------------------------------
	//arg: tagName - name of a tag, case insensitive [string]
	//returns: UID of a Tags_Tag object, or false on failure [string][bool]

	function getTagUID($tagName) {
		global $db;

		$tagName = strtolower(trim($tagName));
		$conditions = array("namelc='" . $tagName . "'");
		$range = $db->loadRange('tags_tag', '*', $conditions);

		foreach($range as $row) { return $row['UID']; }
		return false;
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

	//----------------------------------------------------------------------------------------------
	//. count objects which use this tag (exclude 'suggested' tags)
	//----------------------------------------------------------------------------------------------

	function updateObjectCount() {
		global $db;
		$conditions = array("tagUID='" . $db->addMarkup($this->UID) . "'");
		$this->objectCount = (int)$db->countRange('tags_index', $conditions);
		$this->save();
	}

}

?>
