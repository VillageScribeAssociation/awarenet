<?

//--------------------------------------------------------------------------------------------------------------
//	object for managing user files
//--------------------------------------------------------------------------------------------------------------

class File {

	//------------------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//------------------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure
	var $transforms;	// array of transforms (derivative files)
	var $img;			// File handle

	//------------------------------------------------------------------------------------------------------
	//	constructor
	//------------------------------------------------------------------------------------------------------

	function File($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['fileName'] = '';
		$this->transforms = array();
		if ($UID != '') { $this->load($UID); }
	}

	//------------------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//------------------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('files', $uid, 'true');
		if ($ary == false) { return false; }
		$this->data = $ary;
		$this->expandTransforms();
		return true;
	}
	
	function loadArray($ary) {
		$this->data = $ary;
		$this->expandTransforms();
	}

	//------------------------------------------------------------------------------------------------------
	//	save a record
	//------------------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->data['recordAlias'] = raSetAlias(	'files', $this->data['UID'], 
													$this->data['title'], 'files'	);

		dbSave($this->data, $this->dbSchema); 
	}

	//------------------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//------------------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//------------------------------------------------------------------------------------------------------
	//	sql information
	//------------------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'files';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'refUID' => 'VARCHAR(30)',
			'refModule' => 'VARCHAR(30)',			
			'title' => 'VARCHAR(255)',
			'licence' => 'VARCHAR(100)',
			'attribName' => 'VARCHAR(255)',
			'attribURL' => 'VARCHAR(255)',
			'fileName' => 'VARCHAR(255)',
			'format' => 'VARCHAR(255)',
			'transforms' => 'TEXT',
			'caption' => 'TEXT',
			'category' => 'VARCHAR(100)',
			'weight' => 'VARCHAR(10)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'hitcount' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'refUID' => '10',
			'refModule' => '10',  
			'recordAlias' => '20', 
			'category' => '20' );

		$dbSchema['nodiff'] = array('UID', 'recordAlias', 'hitcount', 'transforms');
		return $dbSchema;
	}

	//------------------------------------------------------------------------------------------------------
	//	return the data
	//------------------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//------------------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//------------------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;	
		
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['dnUrl'] = '';
		$ary['dnLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//----------------------------------------------------------------------------------------------
		//	links
		//----------------------------------------------------------------------------------------------

		if (authHas('files', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%files/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('files', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%files/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('files', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%files/delete/rmfile_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		$ary['dnUrl'] = "%%serverPath%%files/dn/" . $this->data['recordAlias'];
		$ary['dnLink'] = "<a href='" . $ary['dnUrl'] . "'>[download]</a>";
		
		$ary['thumbUrl'] = '/themes/clockface/images/arrow_down.jpg';
		
		return $ary;
	}

	//------------------------------------------------------------------------------------------------------
	//	install this module
	//------------------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing files Module</h3>\n";

		//----------------------------------------------------------------------------------------------
		//	create blog table if it does not exist
		//----------------------------------------------------------------------------------------------
		if (dbTableExists('files') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created files table and indices...<br/>';
		} else {
			$this->report .= 'files table already exists...<br/>';	
		}

		return $report;
	}

	//------------------------------------------------------------------------------------------------------
	//	expand transforms
	//------------------------------------------------------------------------------------------------------

	function expandTransforms() {
		$this->transforms = array();
		$lines = explode("\n", $this->data['transforms']);
		foreach($lines as $line) {
		  $pipe = strpos($line, '|');
		  if ($pipe != false) {
			$transName = substr($line, 0, $pipe);
			$transFile = substr($line, $pipe + 1);
			$this->transforms[$transName] = $transFile;
		  }
		}
	}

	//------------------------------------------------------------------------------------------------------
	//	check if a given transform exists
	//------------------------------------------------------------------------------------------------------

	function hasTrasform($transName) {
		global $installPath;
		if (array_key_exists($transName, $this->transforms) == false) { return false; }
		if (file_exists($installPath . $this->transforms[$transName])) { 
			return $this->transforms[$transName];
		}
	}

	//------------------------------------------------------------------------------------------------------
	//	find a single File on a given record and module
	//------------------------------------------------------------------------------------------------------

	function findSingle($refModule, $refUID, $category) {
		$sql = "select * from files where refModule='" . sqlMarkup($refModule) 
		     . "' and refUID='" . sqlMarkup($refUID) . "' and category = '" . sqlMarkup($category). "'";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { 
			$this->load($row['UID']); 
			return $row['UID']; 
		}

		return false;
	}
	
	//------------------------------------------------------------------------------------------------------
	//	save an File to disk and record the filename in $this->fileName
	//------------------------------------------------------------------------------------------------------

	function storeFile($str) {
		global $installPath;
		
		//----------------------------------------------------------------------------------------------
		//	ensure directory exists
		//----------------------------------------------------------------------------------------------
		$baseDir = $installPath . 'data/files/';
		$baseDir .= substr($this->data['UID'], 0, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->data['UID'], 1, 1) . '/';
		@mkdir($baseDir);
		$baseDir .= substr($this->data['UID'], 2, 1) . '/';
		@mkdir($baseDir);
		
		//----------------------------------------------------------------------------------------------
		//	save the file
		//----------------------------------------------------------------------------------------------
		
		$fileName = $baseDir . $this->data['UID'] . '.xxx';
		
		$fh = fopen($fileName, 'w+');
		fwrite($fh, $str);
		fclose($fh);
		
		$this->data['fileName'] = str_replace($installPath, '', $fileName);
		$this->data['format'] = 'xxx';
	}

	//------------------------------------------------------------------------------------------------------
	//	delete current File and all transforms
	//------------------------------------------------------------------------------------------------------

	function delete() {
		global $installPath;
		if ($this->data['fileName'] == '') { return false; }
		
		//-----------------------------------------------------------------------------------------
		//	delete file from /data/ and any transforms
		//-----------------------------------------------------------------------------------------
		unlink($installPath . $this->data['fileName']);

		$this->expandTransforms();
		foreach($this->transforms as $transName => $fileName) {
			$fileName = $installPath . $fileName;
			unlink($fileName);
		}

		//-----------------------------------------------------------------------------------------
		//	delete the record
		//-----------------------------------------------------------------------------------------		
		dbDelete('files', $this->data['UID']);

		//-----------------------------------------------------------------------------------------
		//	allow other modules to respond to this event
		//-----------------------------------------------------------------------------------------		
		$args = array('module' => 'files', 'UID' => $this->data['UID']);
		eventSendAll('object_deleted', $args);
	}
	
}

?>
