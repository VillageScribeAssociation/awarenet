<?

//--------------------------------------------------------------------------------------------------
//*	Previous version of a file
//--------------------------------------------------------------------------------------------------
//+	when an object is added to the system, a hash (sha1) has is calculated (by the uploader), if 
//+	the hash is different to a version of the object already in the repository then a copy of the 
//+	older version is stored in the revisions table.  Revisions are not editable but may be 
//+	commented on.
//+
//+	content is base64 encoded to prevent escaped characters in code (\r, \n, etc) from being
//+	kludged by MySQL


class Code_Revision {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $fileUID;			//_ ref:Code_File (most recent version of this) [string]
	var $package;			//_ ref:Code_Package (package this belongs/ed to) [string]
	var $parent;			//_ ref:Code_File (usually a folder) [string]
	var $path;				//_ varchar(255) [string]
	var $type;				//_ varchar(50) [string]
	var $title;				//_ varchar(255) [string]
	var $version;			//_ varchar(100) [string]
	var $revision;			//_ varchar(100) [string]
	var $description;		//_ plaintext [string]
	var $content;			//_ text [string]
	var $message;			//_	commit message [string]
	var $hash;				//_ varchar(50) [string]
	var $isBinary;			//_ varchar(10) [string]
	var $fileName;			//_ varchar(255) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Revision object [string]

	function Code_Revision($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Revision object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Revision object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->fileUID = $ary['fileUID'];
		$this->package = $ary['package'];
		$this->parent = $ary['parent'];
		$this->path = $ary['path'];
		$this->type = $ary['type'];
		$this->title = $ary['title'];
		$this->version = $ary['version'];
		$this->revision = $ary['revision'];
		$this->description = $ary['description'];
		$this->content = $ary['content'];
		$this->message = $ary['message'];
		$this->hash = $ary['hash'];
		$this->isBinary = $ary['isBinary'];
		$this->fileName = $ary['fileName'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
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
		$dbSchema['module'] = 'code';
		$dbSchema['model'] = 'code_revision';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'fileUID' => 'VARCHAR(30)',
			'package' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'path' => 'VARCHAR(255)',
			'type' => 'VARCHAR(50)',
			'title' => 'VARCHAR(255)',
			'version' => 'VARCHAR(100)',
			'revision' => 'VARCHAR(100)',
			'description' => 'TEXT',
			'content' => 'TEXT',
			'message' => 'TEXT',
			'hash' => 'VARCHAR(50)',
			'isBinary' => 'VARCHAR(10)',
			'fileName' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'DATETIME'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'package' => '10',
			'parent' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => ''
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'fileUID',
			'package',
			'parent',
			'path',
			'type',
			'title',
			'version',
			'revision',
			'description',
			'content',
			'message',
			'hash',
			'isBinary',
			'fileName'
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
			'fileUID' => $this->fileUID,
			'package' => $this->package,
			'parent' => $this->parent,
			'path' => $this->path,
			'type' => $this->type,
			'title' => $this->title,
			'version' => $this->version,
			'revision' => $this->revision,
			'description' => $this->description,
			'content' => $this->content,
			'message' => $this->message,
			'hash' => $this->hash,
			'isBinary' => $this->isBinary,
			'fileName' => $this->fileName,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
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
		if (true == $user->authHas('code', 'code_revision', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%code/showrevision/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('code', 'code_revision', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%code/editrevision/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('code', 'code_revision', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%code/delrevision/' . $ext['UID'];
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
