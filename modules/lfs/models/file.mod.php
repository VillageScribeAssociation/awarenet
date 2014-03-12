<?

//--------------------------------------------------------------------------------------------------
//*	Metadata object describing a local file.
//--------------------------------------------------------------------------------------------------

class LFS_File {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;			//_ UID [string]
	var $directory;			//_ varchar(255) [string]
	var $directoryUID;			//_ uid [string]
	var $name;			//_ varchar(255) [string]
	var $size;			//_ bigint [string]
	var $sha1;			//_ varchar(50) [string]
	var $md5;			//_ varchar(50) [string]
	var $mime;			//_ varchar(100) [string]
	var $category;			//_ varchar(50) [string]
	var $meta;			//_ text [string]
	var $updatedTo;			//_ datetime [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ shared [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a File object [string]

	function LFS_File($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a File object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load File object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->directory = $ary['directory'];
		$this->directoryUID = $ary['directoryUID'];
		$this->name = $ary['name'];
		$this->size = $ary['size'];
		$this->sha1 = $ary['sha1'];
		$this->md5 = $ary['md5'];
		$this->mime = $ary['mime'];
		$this->category = $ary['category'];
		$this->meta = $ary['meta'];
		$this->updatedTo = $ary['updatedTo'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
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
		$dbSchema['module'] = 'lfs';
		$dbSchema['model'] = 'lfs_file';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'directory' => 'VARCHAR(255)',
			'directoryUID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'size' => 'BIGINT(15)',
			'sha1' => 'VARCHAR(50)',
			'md5' => 'VARCHAR(50)',
			'mime' => 'VARCHAR(100)',
			'category' => 'VARCHAR(50)',
			'meta' => 'TEXT',
			'updatedTo' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'directoryUID' => '10',
			'name' => '10',
			'size' => '10',
			'sha1' => '10',
			'md5' => '10',
			'updatedTo' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'directory',
			'directoryUID',
			'name',
			'size',
			'sha1',
			'md5',
			'mime',
			'category',
			'meta',
			'updatedTo'
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
			'directory' => $this->directory,
			'directoryUID' => $this->directoryUID,
			'name' => $this->name,
			'size' => $this->size,
			'sha1' => $this->sha1,
			'md5' => $this->md5,
			'mime' => $this->mime,
			'category' => $this->category,
			'meta' => $this->meta,
			'updatedTo' => $this->updatedTo,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $kapenta;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $kapenta->user->authHas('lfs', 'lfs_file', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%lfs/showfile/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $kapenta->user->authHas('lfs', 'lfs_file', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%lfs/editfile/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $kapenta->user->authHas('lfs', 'lfs_file', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%lfs/delfile/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
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
