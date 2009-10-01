<?

//--------------------------------------------------------------------------------------------------
//	object for managing records of schools
//--------------------------------------------------------------------------------------------------

class School {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;		// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function School($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['name'] = 'New School';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('schools', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('schools', $d['UID'], $d['name'], 'schools');
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'schools';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'name' => 'VARCHAR(255)',
			'description' => 'TEXT',
			'geocode' => 'TEXT',
			'country' => 'VARCHAR(255)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (authHas('Schools', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%schools/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('Schools', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%schools/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('Schools', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%schools/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('Schools', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%schools/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new school]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = str_replace(">\n", ">", trim($ary['description']));
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
	
		$ary['summary'] = substr(strip_tags(strip_blocks($ary['description'])), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		$ary['descriptionJs'] = $ary['description'];
		$ary['descriptionJs'] = str_replace("'", '--squote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("\"", '--dquote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("[[:", '[[%%delme%%:', $ary['descriptionJs']);	
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Schools Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create Schools table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('schools') == false) {	
			echo "installing schools module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created schools table and indices...<br/>';
		} else {
			$this->report .= 'schools table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		$sql = "delete from images where refModule='schools' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		$sql = "delete from files where refModule='schools' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		
		raDeleteAll('schools', $this->data['UID']);
		dbDelete('schools', $this->data['UID']);
	}

}

?>
