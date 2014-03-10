<?

//--------------------------------------------------------------------------------------------------
//*	Stores current source and resource files
//--------------------------------------------------------------------------------------------------
//+	Content is base64 decoded to prevent escaped characters in code (\r, \n, etc) from being
//+	kludged by MySQL
//+
//+	Binary items such as images are stored separately, in /data/code/ and the content field
//+	set to '(binary file attached)'

class Code_File {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID = '';			//_ UID [string]
	var $package = '';		//_ package this file belongs to, (ref:Code_Package) [string]
	var $parent = '';		//_ link to folder containing this item, (ref:Code_File) [string]
	var $path;				//_ location relative to install (varchar(255)) [string]
	var $type;				//_ MIME type of file (varchar(50)) [string]
	var $title;				//_ display name (varchar(255)) [string]
	var $version;			//_ major version of package varchar(100) [string]
	var $revision;			//_ revision of this file (varchar(100)) [string]
	var $description;		//_ plaintext [string]
	var $content;			//_ file contents, base64 encoded [string]
	var $message;			//_ commit message [string]
	var $size = '0';		//_	file size in bytes (int) [string]
	var $hash = '';			//_ sha1 hash of file contents [string]
	var $isBinary = 'no';	//_ Set to yes if binary file is attached (yes|no) [string]
	var $fileName = '';		//_ basename of file (varchar(255)) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ (ref:Users_User) [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ (ref:Users_User) [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a File object [string]

	function Code_File($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			// initialize
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));
			$this->type = 'txt';
			$this->parent = 'root';
			$this->title = 'New Text File.txt';
			$this->version = '0';
			$this->revision = '0';
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
	//.	load an object from the db given its path
	//----------------------------------------------------------------------------------------------
	//arg: path - file location relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function loadByPath($path) {
		global $kapenta;
		$condititons = array("path='" . $kapenta->db->addMarkup($path) . "'");
		$range = $kapenta->db->loadRange('code_file', '*', $conditions);
	
		if (0 == count($range)) { return false; }

		foreach($range as $item) { $this->loadArray($item); }
		return $this->loaded;
	}

	//----------------------------------------------------------------------------------------------
	//.	load File object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
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
		$this->size = $ary['size'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, HTML warning message if not [string]

	function verify() {
		global $kapenta;
		$report = '';			//%	return value [string]

		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }

		if (false == $kapenta->db->objectExists('code_package', $this->package)) { 
			$report .= "Unkown package.<br/>";
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'code';
		$dbSchema['model'] = 'code_file';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'package' => 'VARCHAR(30)',
			'parent' => 'VARCHAR(30)',
			'path' => 'VARCHAR(255)',
			'type' => 'VARCHAR(50)',
			'title' => 'VARCHAR(255)',
			'version' => 'VARCHAR(100)',
			'revision' => 'VARCHAR(100)',
			'description' => 'TEXT',
			'content' => 'MEDIUMTEXT',
			'message' => 'TEXT',
			'size' => 'VARCHAR(20)',
			'hash' => 'VARCHAR(50)',
			'isBinary' => 'VARCHAR(10)',
			'fileName' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'package' => '10',
			'parent' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
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
			'size',
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
			'package' => $this->package,
			'parent' => $this->parent,
			'path' => $this->path,
			'type' => $this->type,
			'title' => $this->title,
			'version' => $this->version,
			'revision' => $this->revision,
			'description' => $this->description,
			'content' => $this->content,
			'message' => $this->content,
			'size' => $this->size,
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
		if (true == $user->authHas('code', 'code_file', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%code/show/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('code', 'code_file', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%code/editfile/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('code', 'code_file', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%code/confirmdelete/UID_' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	safeContent - content with htmlEntities replaced so it can go in a textarea
		//------------------------------------------------------------------------------------------

		$ext['safeContent'] = stripslashes(base64_decode($ext['content']));
		$ext['safeContent'] = str_replace('<', '&lt;', $ext['safeContent']);
		$ext['safeContent'] = str_replace('>', '&gt;', $ext['safeContent']);
		$ext['safeContent'] = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $ext['safeContent']);
		$ext['safeContent'] = str_replace('[[:', '[[%%delme%%:', $ext['safeContent']);
		
		//------------------------------------------------------------------------------------------
		//	summary / html
		//------------------------------------------------------------------------------------------
		$ext['contentHtml'] = str_replace(">\n", ">", $ext['content']);
		$ext['contentHtml'] = str_replace("\r", "", $ext['contentHtml']);

		$ext['descriptionJs'] = $ext['description'];
		$ext['descriptionJs'] = str_replace("'", '--squote--', $ext['descriptionJs']);
		$ext['descriptionJs'] = str_replace("'", '--dquote--', $ext['descriptionJs']);
	
		$ext['summary'] = substr(strip_tags($ext['content']), 0, 400) . '...';

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
	//.	decide which type of file this is
	//----------------------------------------------------------------------------------------------
	//arg: path - full path of file [string]
	//returns: best guess at item type, given file path [string]
	//;	note that in future versions we may examine the content of files, this will do for now.

	function getType($path) {
		$type = 'unknown';

		$exts = array(
			'.jpg' => 'image/jpeg',
			'.jpeg' => 'image/jpeg',
			'.png' => 'image/png',
			'.txt' => 'text/plain',
			'.html' => 'text/html',
			'.act.php' => 'kapenta/action',
			'.block.php' => 'kapenta/block',
			'.page.php' => 'kapenta/page',
			'.fn.php' => 'kapenta/view',
			'.inc.php' => 'kapenta/include',
			'.on.php' => 'kapenta/event',
			'.class.php' => 'kapenta/class',
			'.log.php' => 'kapenta/log',
			'.module.xml.php' => 'kapenta/module',
			'.xml.php' => 'kapenta/xml',
			'.php' => 'text/php',
			'.js' => 'text/javascript',
		);

		//TODO

		return $type;
	}

	//----------------------------------------------------------------------------------------------
	//.	get relative path of this object
	//----------------------------------------------------------------------------------------------
	//returns: string describing this items location in tree [string]

	function getRelPath() {
		$countdown = 10; 						//% to prevent circular refs crashing this [int]
		$relPath = $this->title;				//%	return value [string]
		$curr = new Code_File($this->UID);		//%	[object]

		while ($curr->parent != 'none') {
			$countdown--;
			if ($counddown == 0) { break; }

			echo "title: ". $curr->title ." (parent: " . $curr->parent . ")<br/>\n";
			if (($curr->parent != 'none') AND ($curr->parent != '')) {
				$curr->load($this->parent);
				$relPath = $curr->title . $relPath;
			} else {
				$curr->parent = 'none';
			}
		}

		return $relPath;
	}

}

?>
