<?

//--------------------------------------------------------------------------------------------------------------
//*	object representing static pages
//--------------------------------------------------------------------------------------------------------------

class StaticPage {

	//------------------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//------------------------------------------------------------------------------------------------------

	var $data;			// object data [array]
	var $dbSchema;		// database structure [array]

	//------------------------------------------------------------------------------------------------------
	//.	constructor
	//------------------------------------------------------------------------------------------------------
	//opt: raUID - recordAlias or UID of a static page [string]

	function StaticPage($raUID = '') {
		global $db;

		$this->dbSchema = $this->getDbSchema();
		$this->data = $db->makeBlank($this->dbSchema);
		$this->title == 'New Static Page';
		if ($raUID != '') { $this->load($raUID); }
	}

	//------------------------------------------------------------------------------------------------------
	//.	load a record by UID or recordAlias
	//------------------------------------------------------------------------------------------------------
	//arg: raUID - recordAlias or UID of a school [string]

	function load($raUID) {
		global $db;

		$ary = $db->loadAlias('static', $raUID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
	global $db;

		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$ra = raSetAlias('static', $this->UID, $this->title, 'static');
		$this->alias = $ra;
		$db->save($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['model'] = 'static';
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
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'alias' => 'VARCHAR(255)' );

		$dbSchema['indices'] = array('UID' => '10', 'alias' => '20');
		$dbSchema['nodiff'] = array('UID', 'alias');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current object from the database
	//----------------------------------------------------------------------------------------------

	function delete() {
	global $db;

		if ($user->authHas('home', 'Home_Static', 'delete', 'TODO:UIDHERE') == false) { return false; }
		$db->delete('static', $this->UID);

		// allow other modules to respond to this event
		$args = array('module' => 'static', 'UID' => $this->UID);
		eventSendAll('object_deleted', $args);
	}
	
	//----------------------------------------------------------------------------------------------
	//.	serialize this object as an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

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

		if ($user->authHas('home', 'Home_Static', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%static/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[permalink]</a>"; 
		}

		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%static/edit/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) { 
				$ary['newUrl'] = "%%serverPath%%static/new/"; 
				$ary['newLink'] = "<a href='" . $newUrl . "'>[new]</a>";
		}
		
		if ($user->authHas('home', 'Home_Static', 'edit', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%static/confirmdelete/' . $this->alias;
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

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
	global $db;

		$report = "<h3>Installing Static Pages Module</h3>\n";

		//----------------------------------------------------------------------------------------------
		//	create static table if it does not exist
		//----------------------------------------------------------------------------------------------
		
		if ($db->tableExists('static') == false) {	
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
