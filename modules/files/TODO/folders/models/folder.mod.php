<?

//--------------------------------------------------------------------------------------------------
//*	object for user folders
//--------------------------------------------------------------------------------------------------
//+	Folders can nest, parent may be 'root' or the UID of another folder, users can manage their 
//+	own folders, admins can change any folder.  Description field is currently unused, same as 
//+	galleries.
//+	
//+	children and files fields contain simple serialized arrays of precached queries.  children
//+	array does not contain these two fields.
//+
//+	Might add: thumbnails, as opposed to generic icons for file types

class Folder {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $children;		// subfolders (array of)
	var $files;			// from files table (array of)
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or recordAlias of a folder [string]

	function Folder($raUID = '') {
		global $db;

		global $user;
		$this->dbSchema = $this->getDbSchema();
		$this->data = $db->makeBlank($this->dbSchema);
		$this->parent = 'root';
		$this->title = 'New folder ' . $this->UID;
		$this->children = '';
		$this->files = '';
		if ($raUID != '') { $this->load($raUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or recordAlias of a folder record [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;

		$ary = $db->loadAlias($raUID, $this->dbSchema);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
		$this->children = unzerialize($ary['children']);
		$this->files = unzerialize($ary['files']);
	}

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		global $db;

		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->children = serialize($this->children);
		$this->files = serialize($this->files);

		$this->alias = raSetAlias(	'folder', $this->UID, 
													$this->title, 'folder'	);

		$db->save($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';

		// strip out all chars except for A-Z, a-z, 0-9, -, _ and space
		$this->title = str_replace(' ', '--space--', $this->title);
		$this->title = mkAlphaNumeric($this->title); // in /core/utils.inc.php
		$this->title = str_replace('--space--', ' ', $this->title);

		// checks
		if (strlen($this->UID) < 5) { $verify .= "UID not present.\n"; }
		if (trim($this->title) == '') { $verify .= "folder must have a name.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['model'] = 'folders';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'alias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'parent' => 10, 
			'createdBy' => '10', 
			'alias' => '20' );

		$dbSchema['nodiff'] = array('UID', 'alias', 'children', 'files');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		global $theme;
		global $kapenta;

		$ary = $this->data;	

		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		$ary['addChildUrl'] = '';
		$ary['addChildLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ('admin' == $user->role) { $auth = true; }
		if ($user->UID == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('files', 'files_folder', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%folders/' . $ary['alias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%folders/edit/' . $ary['alias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%folders/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new folder]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%folders/addchild/" . $ary['alias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child folder]</a>";  
			$ary['delUrl'] = "%%serverPath%%folders/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete folder]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = $kapenta->longDate($ary['date']);
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['folderTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	format for WYSWYG editor
		//------------------------------------------------------------------------------------------

		$ary['descriptionJs'] = $ary['description'];
		$ary['descriptionJs'] = str_replace("'", '--squote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("'", '--dquote--', $ary['descriptionJs']);
	
		$ary['summary'] = $theme->makeSummary($ary['description'], 400);

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users_User($ary['createdBy']);
		$ary['userName'] = $model->firstname . ' ' . $model->surname;		
		$ary['userRa'] = $model->alias;
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
	global $db;

		$report = "<h3>Installing folder Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create folder table if it does not exist
		//------------------------------------------------------------------------------------------

		if ($db->tableExists('folder') == false) {	
			echo "installing folder module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created folder table and indices...<br/>';
		} else {
			$this->report .= 'folder table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		global $db;

		//------------------------------------------------------------------------------------------
		//	delete subfolders
		//------------------------------------------------------------------------------------------
		/*
		foreach($this->children as $UID => $ary) {
			$model = new Folder($ary['UID']);
			$model->delete();
		}
		*/
		//TODO: event handler should take care of this

		//------------------------------------------------------------------------------------------
		//	delete files
		//------------------------------------------------------------------------------------------
		/*
		foreach($this->files as $UID => $ary) {
			$model = new Files($ary['UID']);
			$model->delete();
		}
		*/
		//TODO: event handler should take care of this

		//------------------------------------------------------------------------------------------
		//	delete this record and its recordAlias
		//------------------------------------------------------------------------------------------
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	update array of children
	//----------------------------------------------------------------------------------------------

	function updateChildren() {
		global $db;

		$this->children = array();

		$sql = "select * from folders where parent='" . $this->UID . "' order by title";
		//TODO: $db->loadRange

		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);

			$childSubfolders = unserialize($row['children']);	// TODO: use XML?
			$row['children'] = '';								// remove list of subfolders
			$row['childrenCount'] = count($childSubfolders);	// but retain count

			$childFiles = unserialize($row['files']);			// TODO: use XML?
			$row['files'] = '';									// remove list of files
			$row['fileCount'] = count($childFiles);				// but retain count

			$this->children[$row['UID']] = $row;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	update array of files
	//----------------------------------------------------------------------------------------------

	function updateFiles() {
	global $db;

		$this->files = array();

		$sql = "select * from files "
			 . "where refModule='folders' and refUID='" . $this->UID . "' "
			 . "order by title";

		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$this->files[$row['UID']] = $row;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	update parent (for when this record changes name, description)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function updateParent() {
		if ($this->parent == 'root') { return false; }
		$model = new Folder($this->parent);
		$model->updateChildren();
		$model->save();
		return true();
	}
}

?>
