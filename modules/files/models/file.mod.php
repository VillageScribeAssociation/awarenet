<?

//--------------------------------------------------------------------------------------------------------------
//*	object for managing user files
//--------------------------------------------------------------------------------------------------------------

class Files_File {

	//------------------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//------------------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $refModule;			//_ module [string]
	var $refModel;			//_ model [string]
	var $refUID;			//_ ref:*-* [string]
	var $title;				//_ title [string]
	var $licence;			//_ varchar(100) [string]
	var $attribName;		//_ varchar(255) [string]
	var $attribUrl;			//_ varchar(255) [string]
	var $fileName;			//_ varchar(255) [string]
	var $fileSize;			//_ large integer [string]
	var $hash;				//_ sha1 hash of file contents [string]
	var $format;			//_ varchar(255) [string]
	var $transforms;		//_ plaintext [string]
	var $caption;			//_ plaintext [string]
	var $weight;			//_ int [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $shared;			//_ shared with other peers (yes|no) [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a File object [string]

	function Files_File($raUID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { $this->load($raUID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a File object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load File object serialized as an associative array
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
		$this->title = $ary['title'];
		$this->licence = $ary['licence'];
		$this->attribName = $ary['attribName'];
		$this->attribUrl = $ary['attribUrl'];
		$this->fileName = $ary['fileName'];
		$this->fileSize = $ary['fileSize'];
		$this->hash = $ary['hash'];
		$this->format = $ary['format'];
		$this->transforms = $ary['transforms'];
		$this->caption = $ary['caption'];
		$this->weight = $ary['weight'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->alias = $ary['alias'];
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$this->alias = $aliases->create('files', 'files_file', $this->UID, $this->title);
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		global $kapenta;

		$report = '';			//%	return value [string]

		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }

		if ($kapenta->fs->exists($this->fileName)) {
			if (0 == (int)$this->fileSize) {
				$this->fileSize = $kapenta->fs->size($this->fileName);
			}
			if ('' == $this->hash) {
				$this->hash = $kapenta->fileSha1($this->fileName);
			}
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'files';
		$dbSchema['model'] = 'files_file';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'licence' => 'VARCHAR(100)',
			'attribName' => 'VARCHAR(255)',
			'attribUrl' => 'VARCHAR(255)',
			'fileName' => 'VARCHAR(255)',
			'fileSize' => 'BIGINT(20)',
			'hash' => 'VARCHAR(50)',
			'format' => 'VARCHAR(255)',
			'transforms' => 'TEXT',
			'caption' => 'TEXT',
			'weight' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'shared' => 'VARCHAR(3)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

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
			'title' => $this->title,
			'licence' => $this->licence,
			'attribName' => $this->attribName,
			'attribUrl' => $this->attribUrl,
			'fileName' => $this->fileName,
			'fileSize' => $this->fileSize,
			'hash' => $this->hash,
			'format' => $this->format,
			'transforms' => $this->transforms,
			'caption' => $this->caption,
			'weight' => $this->weight,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		global $utils;

		$ary = $this->toArray();	
		
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['dnUrl'] = '';
		$ary['dnLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('files', 'files_file', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%files/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $user->authHas('files', 'files_file', 'edit', $this->UID)) { 
			$ary['editUrl'] =  '%%serverPath%%files/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $user->authHas('files', 'files_file', 'delete', $this->UID)) { 
			$ary['delUrl'] =  '%%serverPath%%files/delete/rmfile_' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		$ary['dnUrl'] = "%%serverPath%%files/dn/" . $this->alias;
		$ary['dnLink'] = "<a href='" . $ary['dnUrl'] . "'>[download]</a>";
		
		$ary['thumbUrl'] = '%%serverPath%%/themes/%%defaultTheme%%/images/icons/arrow_down.jpg';

		$ary['printFileSize'] = $utils->printFileSize((int)$ary['fileSize']);
	
		return $ary;
	}

	//------------------------------------------------------------------------------------------------------
	//.	find a single file on a given record and module
	//------------------------------------------------------------------------------------------------------
	//arg: refModule - a kapenta module [string]
	//arg: refMmodel - a model name [string]
	//arg: refUID - UID of object which owns the file [string]
	//returns: file record in array form, or false if not found [array][false]
	//TODO: discover if this is used by anything, remove if not

	function findSingle($refModule, $refModel, $refUID) {
		global $db;

		// $sql = "select * from Files_File where refModule='" . $db->addMarkup($refModule) 
		//    . "' and refUID='" . $db->addMarkup($refUID) . "' "
		//	  . " and category = '" . $db->addMarkup($category). "'";

		$conditions = array();
		$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
		$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

		$range = $db->loadRange('files_file', '*', $conditions);

		foreach ($range as $row) { 
			$this->load($row['UID']); 
			return $row['UID']; 
		}

		return false;
	}
	
	//------------------------------------------------------------------------------------------------------
	//.	save a file to disk and record the filename in $this->fileName
	//------------------------------------------------------------------------------------------------------
	//arg: str - file contents [string]

	function storeFile($str) {
		global $kapenta;
		//TODO: use $kapenta to make the directories, write content, etc
		
		//----------------------------------------------------------------------------------------------
		//	ensure directory exists
		//----------------------------------------------------------------------------------------------
		$baseDir = $kapenta->installPath . 'data/files/';
		$baseDir .= substr($this->UID, 0, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->UID, 1, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->UID, 2, 1) . '/';
		@mkdir($baseDir);
		
		//----------------------------------------------------------------------------------------------
		//	save the file
		//----------------------------------------------------------------------------------------------
		
		$fileName = $baseDir . $this->UID . '.xxx';
		
		$fh = fopen($fileName, 'w+');
		fwrite($fh, $str);
		fclose($fh);
		
		$this->fileName = str_replace($kapenta->installPath, '', $fileName);
		$this->format = 'xxx';
	}

	//------------------------------------------------------------------------------------------------------
	//.	delete current file and all transforms
	//------------------------------------------------------------------------------------------------------

	function delete() {
		global $kapenta;
		global $db;

		if ($this->fileName == '') { return false; }
		
		//-----------------------------------------------------------------------------------------
		//	delete file from /data/ and any transforms
		//-----------------------------------------------------------------------------------------
		unlink($kapenta->installPath . $this->fileName);

		foreach($this->transforms as $transName => $fileName) {
			$fileName = $kapenta->installPath . $fileName;
			unlink($fileName);						//TODO: $kapenta should do this
		}

		//-----------------------------------------------------------------------------------------
		//	delete the record
		//-----------------------------------------------------------------------------------------		
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}
	
}

?>
