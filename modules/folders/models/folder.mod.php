<?

//--------------------------------------------------------------------------------------------------
//	object for user folders
//--------------------------------------------------------------------------------------------------

//	Folders can nest, parent may be 'root' or the UID of another folder, users can manage their 
//	own folders, admins can change any folder.  Description field is currently unused, same as 
//	galleries.
//	
//	children and files fields contain simple serialized arrays of precached queries.  children
//	array does not contain these two fields.
//
//	Might add: thumbnails, as opposed to generic icons for file types

class Folder {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $children;		// subfolders (array of)
	var $files;			// from files table (array of)
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Folder($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['parent'] = 'root';
		$this->data['title'] = 'New folder ' . $this->data['UID'];
		$this->data['children'] = '';
		$this->data['files'] = '';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('folders', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
		$this->children = unzerialize($ary['children']);
		$this->files = unzerialize($ary['files']);
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->data['children'] = serialize($this->children);
		$this->data['files'] = serialize($this->files);

		$this->data['recordAlias'] = raSetAlias(	'folder', $this->data['UID'], 
													$this->data['title'], 'folder'	);

		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		// strip out all chars except for A-Z, a-z, 0-9, -, _ and space
		$this->data['title'] = str_replace(' ', '--space--', $this->data['title']);
		$this->data['title'] = mkAlphaNumeric($this->data['title']); // in /core/utils.inc.php
		$this->data['title'] = str_replace('--space--', ' ', $this->data['title']);

		// checks
		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (trim($this->data['title']) == '') { $verify .= "folder must have a name.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'folders';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'createdBy' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'parent' => 10, 
			'createdBy' => '10', 
			'recordAlias' => '20' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias', 'children', 'files');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		global $user;
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
		if ($user->data['ofGroup'] == 'admin') { $auth = true; }
		if ($user->data['UID'] == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('folder', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%folders/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%folders/edit/' . $ary['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%folders/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new folder]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%folders/addchild/" . $ary['recordAlias'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child folder]</a>";  
			$ary['delUrl'] = "%%serverPath%%folders/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete folder]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = date('jS F, Y', strtotime($ary['date']));
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['folderTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	summary
		//------------------------------------------------------------------------------------------

		$ary['summary'] = strip_tags($ary['description']);
		$ary['summary'] = substr($ary['summary'], 0, 1000) . '...';
	
		//------------------------------------------------------------------------------------------
		//	format for WYSWYG editor
		//------------------------------------------------------------------------------------------

		$ary['descriptionJs'] = $ary['description'];
		$ary['descriptionJs'] = str_replace("'", '--squote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("'", '--dquote--', $ary['descriptionJs']);
	
		$ary['summary'] = substr(strip_tags($ary['description']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users($ary['createdBy']);
		$ary['userName'] = $model->data['firstname'] . ' ' . $model->data['surname'];		
		$ary['userRa'] = $model->data['recordAlias'];
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing folder Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create folder table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('folder') == false) {	
			echo "installing folder module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created folder table and indices...<br/>';
		} else {
			$this->report .= 'folder table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	delete a record (including all files and subfolders)
	//----------------------------------------------------------------------------------------------

	function delete() {

		//------------------------------------------------------------------------------------------
		//	delete subfolders
		//------------------------------------------------------------------------------------------
		foreach($this->children as $UID => $ary) {
			$model = new Folder($ary['UID']);
			$model->delete();
		}

		//------------------------------------------------------------------------------------------
		//	delete files
		//------------------------------------------------------------------------------------------
		foreach($this->files as $UID => $ary) {
			$model = new Files($ary['UID']);
			$model->delete();
		}

		//------------------------------------------------------------------------------------------
		//	delete this record and its recordAlias
		//------------------------------------------------------------------------------------------
		raDeleteAll('folder', $this->data['UID']);
		dbDelete('folder', $this->data['UID']);

	}

	//----------------------------------------------------------------------------------------------
	//	update array of children
	//----------------------------------------------------------------------------------------------

	function updateChildren() {
		$this->children = array();

		$sql = "select * from folders where parent='" . $this->data['UID'] . "' order by title";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

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
	//	update array of files
	//----------------------------------------------------------------------------------------------

	function updateFiles() {
		$this->files = array();

		$sql = "select * from files "
			 . "where refModule='folders' and refUID='" . $this->data['UID'] . "' "
			 . "order by title";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$this->files[$row['UID']] = $row;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	update parent (for when this record changes name, description)
	//----------------------------------------------------------------------------------------------

	function updateParent() {
		if ($this->data['parent'] == 'root') { return false; }
		$model = new Folder($this->data['parent']);
		$model->updateChildren();
		$model->save();
		return true();
	}
}

?>
