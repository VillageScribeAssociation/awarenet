<?

//--------------------------------------------------------------------------------------------------------------
//	object for managing static pages
//--------------------------------------------------------------------------------------------------------------

class StaticPage {

	//------------------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//------------------------------------------------------------------------------------------------------

	var $data;		// currently loaded record
	var $dbSchema;		// database structure

	//------------------------------------------------------------------------------------------------------
	//	constructor
	//------------------------------------------------------------------------------------------------------

	function StaticPage($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['title'] == 'New Static Page';
		$this->data['createdOn'] = mysql_datetime();
		$this->data['createdBy'] = $_SESSION['sUserUID'];
		if ($UID != '') { $this->load($UID); }
	}

	//------------------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//------------------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('static', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//------------------------------------------------------------------------------------------------------
	//	save a record
	//------------------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$d = $this->data;
		$this->data['recordAlias'] = raSetAlias('static', $d['UID'], $d['title'], 'static');
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
		$dbSchema['table'] = 'static';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'title' => 'VARCHAR(255)',
			'menu1' => 'TEXT',
			'menu2' => 'TEXT',
			'content' => 'TEXT',	
			'nav1' => 'TEXT',
			'nav2' => 'TEXT',
			'script' => 'TEXT',
			'head' => 'TEXT',
			'createdOn' => 'DATETIME',	
			'createdBy' => 'VARCHAR(30)',
			'recordAlias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'recordAlias' => '20');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;
	}

	//------------------------------------------------------------------------------------------------------
	//	delete the current record
	//------------------------------------------------------------------------------------------------------

	function delete() {
		if (authHas('static', 'delete', '') == false) { return false; }
		dbDelete('static', $this->data['UID']);
		raDeleteAll('static', $this->data['UID']);
	}
	
	//------------------------------------------------------------------------------------------------------
	//	return the data
	//------------------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//------------------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//------------------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;
		$ary['editLink'] = '';
		$ary['viewLink'] = '';
		$ary['newLink'] = '';
		$ary['delLink'] = '';
		
		$ary['editUrl'] = '';
		$ary['viewUrl'] = '';
		$ary['newUrl'] = '';
		$ary['newLink'] = '';
		

		//----------------------------------------------------------------------------------------------
		//	links
		//----------------------------------------------------------------------------------------------

		if (authHas('static', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%static/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[permalink]</a>"; 
		}

		if (authHas('static', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%static/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('static', 'edit', $this->data)) { 
				$ary['newUrl'] = "%%serverPath%%static/new/"; 
				$ary['newLink'] = "<a href='" . $newUrl . "'>[new]</a>";
		}
		
		if (authHas('static', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%static/confirmdelete/' . $this->data['recordAlias'];
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}

		//----------------------------------------------------------------------------------------------
		//	done
		//----------------------------------------------------------------------------------------------
	
		$ary['contentJs'] = $ary['content'];
		$ary['contentJs'] = str_replace("'", '--squote--', $ary['contentJs']);
		$ary['contentJs'] = str_replace("'", '--dquote--', $ary['contentJs']);

		return $ary;
	}

	//------------------------------------------------------------------------------------------------------
	//	install this module
	//------------------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Static Pages Module</h3>\n";

		//----------------------------------------------------------------------------------------------
		//	create static table if it does not exist
		//----------------------------------------------------------------------------------------------
		
		if (dbTableExists('static') == false) {	
			echo "installing static module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created static table and indices...<br/>';
		} else {
			$this->report .= 'static table already exists...<br/>';	
		}

		return $report;
	}

}

?>