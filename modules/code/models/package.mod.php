<?php

	require_once($kapenta->installPath . 'modules/code/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	A set of files published as one unit
//--------------------------------------------------------------------------------------------------

class Code_Package {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;					//_	database table definition [array]
	var $loaded;					//_	set to true when an object has been loaded [bool]

	var $UID = '';					//_ UID [string]
	var $name = '';					//_ title [string]
	var $description = '';			//_ wyswyg [string]
	var $version = '';				//_ major version [string]
	var $revision = '';				//_ incremented each commit (varchar(100)) [string]
	var $includes = '';				//_ filename patterns to include on commit [string]
	var $excludes = '';				//_	filename patterns to exclude on commit [string]
	var $installFile = '';			//_	package install script [string]
	var $installFn = '';			//_	function to call in install script [string]
	var $createdOn;					//_ datetime [string]
	var $createdBy;					//_ ref:users_user [string]
	var $editedOn;					//_ datetime [string]
	var $editedBy;					//_ ref:users_user [string]
	var $alias;						//_ alias [string]

	var $rootFolder = '';			//_	UID of root folder [string]

	var $userIndex;					//_	set of users related to this package [array]
	var $userIndexLoaded = false;	//_	set to true when range loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Package object [string]

	function Code_Package($raUID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		//	initialise table schema
		if ('' != $raUID) { $this->load($raUID); }	// try load an object from the database
		if (false == $this->loaded) {				// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize blank
			$this->name = 'New Package ' . $this->UID;
			$this->description = 'Describe your package here.';
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Package object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID = '') {
		global $kapenta;
		$objary = $kapenta->db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Package object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->description = $ary['description'];
		$this->version = $ary['version'];
		$this->revision = $ary['revision'];
		$this->includes = $ary['includes'];
		$this->excludes = $ary['excludes'];
		$this->installFile = $ary['installFile'];
		$this->installFn = $ary['installFn'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];

		$this->rootFolder = $this->getRootFolder();

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases, $kapenta;

		//echo "<fail>saving package</fail>\n";
		//die();

		$report = $this->verify();
		if ('' != $report) { return $report; }

	//	echo "<fail>verified package</fail>\n";
	//	die();

		$this->alias = $aliases->create('code', 'code_package', $this->UID, $this->name);

	//	echo "<fail>alias created</fail>\n";
	//	print_r($this->toArray());
	//	die();

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
		$dbSchema['module'] = 'code';
		$dbSchema['model'] = 'code_package';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'version' => 'VARCHAR(100)',
			'revision' => 'VARCHAR(100)',
			'includes' => 'TEXT',
			'excludes' => 'TEXT',
			'installFile' => 'VARCHAR(255)',
			'installFn' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'alias' => 'VARCHAR(255)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'name' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '',
			'alias' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'name',
			'description',
			'revision'
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
			'name' => $this->name,
			'description' => $this->description,
			'version' => $this->version,
			'revision' => $this->revision,
			'includes' => $this->includes,
			'excludes' => $this->excludes,
			'installFile' => $this->installFile,
			'installFn' => $this->installFn,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
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
		if (true == $kapenta->user->authHas('code', 'code_package', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%code/showpackage/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $kapenta->user->authHas('code', 'code_package', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%code/editpackage/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $kapenta->user->authHas('code', 'code_package', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%code/delete/' . $ext['alias'];
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

	//----------------------------------------------------------------------------------------------
	//.	get list of patterns used to include filenames in this package on commit
	//----------------------------------------------------------------------------------------------

	function getIncludes() {
		$patterns = array();						//%	return value [array]
		$lines = explode("\n", $this->includes);	//%	[array:string]
		foreach($lines as $line) {
			if ('' != trim($line)) { $patterns[] = trim($line); }
		}

		return $patterns;
	}

	//----------------------------------------------------------------------------------------------
	//.	get list of patterns used to exclude filenames from this package on commit
	//----------------------------------------------------------------------------------------------

	function getExcludes() {
		$patterns = array();						//%	return value [array]
		$lines = explode("\n", $this->excludes);	//%	[array:string]
		foreach($lines as $line) {
			if ('' != trim($line)) { $patterns[] = trim($line); }
		}

		return $patterns;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize to XML
	//----------------------------------------------------------------------------------------------

	function toXml() {
		global $kapenta;

		$xml = '';						//%	return value [string]
		$filter = '';

		//------------------------------------------------------------------------------------------
		//	add the filter
		//------------------------------------------------------------------------------------------
		$includes = $this->getIncludes();	
		foreach($includes as $match) { $filter .= "\t\t<include>" . $match . "</include>\n"; }

		$excludes = $this->getExcludes();	
		foreach($excludes as $match) { $filter .= "\t\t<exclude>" . $match . "</exclude>\n"; }

		//------------------------------------------------------------------------------------------
		//	add files
		//------------------------------------------------------------------------------------------
		#$range = $this->getFiles();			//	uses too much memory for very large packages
		#foreach($range as $item) { removed }	//	removed

		$sql = "select * from code_file where package='" . $kapenta->db->addMarkup($this->UID) . "'";
		$result = $kapenta->db->query($sql);

		while($row = $kapenta->db->fetchAssoc($result)) { 
			$item = $kapenta->db->rmArray($row);
			$xml .= ''
			 . "\t\t<file>\n"
			 . "\t\t\t<uid>" . $item['UID'] . "</uid>\n"
			 . "\t\t\t<hash>" . $item['hash'] . "</hash>\n"
			 . "\t\t\t<type>" . $item['type'] . "</type>\n"
			 . "\t\t\t<size>" . $item['size'] . "</size>\n"
			 . "\t\t\t<path>" . $item['path'] . "</path>\n"
			 . "\t\t</file>\n";
		}

		//------------------------------------------------------------------------------------------
		//	add dependencies
		//------------------------------------------------------------------------------------------
		$dependencies = '';
		//TODO: this

		//------------------------------------------------------------------------------------------
		//	put it all together
		//------------------------------------------------------------------------------------------
		$xml = ''
		. "<package>\n"
		. "\t<uid>" . $this->UID . "</uid>\n"
		. "\t<name>" . $this->name . "</name>\n"
		. "\t<description>" . $this->description . "</description>\n"
		. "\t<version>" . $this->version . "</version>\n"
		. "\t<revision>" . $this->revision . "</revision>\n"
		. "\t<updated>" . $this->editedOn . "</updated>\n"
		. "\t<installfile>" . $this->installFile . "</installfile>\n"
		. "\t<installfn>" . $this->installFn . "</installfn>\n"
		. "\t<files>\n"
		. $xml
		. "\t</files>\n"
		. "\t<dependencies>\n"
		. $dependencies
		. "\t</dependencies>\n"
		. "\t<filter>\n"
		. $filter
		. "\t</filter>\n"
		
		. "</package>\n";

		return $xml;
	}

	//==============================================================================================
	//	USER INDEX
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	load user index if not already done
	//----------------------------------------------------------------------------------------------

	function loadUserIndex() {
		if (false == $this->userIndexLoaded) {
			$this->userIndex = $this->getUserIndex();
			$this->userIndexLoaded = true;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	gets all users who have some relationship to this package
	//----------------------------------------------------------------------------------------------
	//returns: set of serialized Code_UserIndex objects [array]

	function getUserIndex() {
		global $kapenta;
		$conditions = array("packageUID='" . $this->UID . "'");
		$range = $kapenta->db->loadRange('code_userindex', '*', $conditions);
		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a user has a given privilege
	//----------------------------------------------------------------------------------------------
	//returns: true is privilege exists for this user, false if not [bool]

	function hasPrivilege($userUID, $privilege) {
		$this->loadUserIndex();
		foreach($this->userIndex as $itm) {
			if (($itm['userUID'] == $userUID) && ($itm['privilege'] == $privilege)) { return true; }
		}
		return false;
	}

	//==============================================================================================
	//	DEPENDENCIES
	//==============================================================================================

	//TODO: use index table and Code_Dependency object

	//==============================================================================================
	//	FILES AND FOLDERS BELONGING TO THIS PACKAGE
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	get files belonging to this package
	//----------------------------------------------------------------------------------------------
	//returns: set of Code_File objects [array]

	function getFiles() {
		global $kapenta;
		$conditions = array("package='" . $kapenta->db->addMarkup($this->UID) . "'");
		$range = $kapenta->db->loadRange('code_file', '*', $conditions);
		return $range;
	}

	//==============================================================================================
	//	FOLDERS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	get root folder for this package
	//----------------------------------------------------------------------------------------------
	//returns: UID of root folder, creating it if need be, empty string on failure [string]

	function getRootFolder() {
		global $kapenta;
		global $session;

        if ('' == $this->name) { return ''; }

		$conditions = array();
		$conditions[] = "parent='root'";
		$conditions[] = "package='" . $kapenta->db->addMarkup($this->UID) . "'";
		$conditions[] = "type='" . $kapenta->db->addMarkup('folder') . "'";

		$range = $kapenta->db->loadRange('code_file', '*', $conditions);
		if (0 == count($range)) { 
            
			$kapenta->session->msg("package.mod: Package root folder does not exist, creating... " . $this->name);
			return $this->createRootFolder(); 
		}
		
		foreach($range as $item) { return $item['UID']; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	create root folder for this project
	//----------------------------------------------------------------------------------------------
	//returns: UID of root folder object [string]

	function createRootFolder() {
		$model = new Code_File();
		$model->package = $this->UID;
		$model->parent = 'root';
		$model->type = 'folder';
		$model->title = '~/';
		$model->path = '';
		$model->version = '0';
		$model->revision = '0';
		$model->description = 'Root folder of ' . $this->name . ' package.';
		$model->content = '';
		$model->save();

		return $model->UID;
	}

	//----------------------------------------------------------------------------------------------
	//.	get UID of parent folder for a given path, create if it does not exist
	//----------------------------------------------------------------------------------------------
	//returns: UID of folder [string]

	function getParentFolder($path) {
		global $kapenta;
		global $kapenta;

		$parent = $this->getRootFolder();			//%	start from root [string]
		$path = dirname($path);
		$build = '';
		if ('.' == $path) { return $parent; }

		$parts = explode('/', $path);
		foreach($parts as $part) {

			$conditions = array();
			$conditions[] = "package='" . $kapenta->db->addMarkup($this->UID) . "'";
			$conditions[] = "parent='" . $kapenta->db->addMarkup($parent) . "'";
			$conditions[] = "type='" . $kapenta->db->addMarkup('folder') . "'";
			$conditions[] = "path='" . $kapenta->db->addMarkup($build . $part . '/') . "'";

			$range = $kapenta->db->loadRange('code_file', '*', $conditions);

			foreach($range as $item) { $parent = $item['UID']; }

			$build = $build . $part . '/';

			if (0 == count($range)) {
				$model = new Code_File();
				$model->UID = $kapenta->createUID();
				$model->package = $this->UID;
				$model->parent = $parent;
				$model->title = './' . $part;
				$model->type = 'folder';
				$model->path = $build;
				$report = $model->save();
				if ('' != $report) { echo $report; }
				$parent = $model->UID;
			}

		}

		return $parent;
	}

}

?>
