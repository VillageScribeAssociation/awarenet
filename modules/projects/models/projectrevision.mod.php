<?

//--------------------------------------------------------------------------------------------------
//*	object to represent project revisions
//--------------------------------------------------------------------------------------------------
//+	since the projects module is derived from the wiki module, changes to wiki revisions model
//+	should be copied here.

class ProjectRevision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database table structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a project revision [string]

	function ProjectRevision($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a project revision given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a project revision [string]
	//returns: true if object is found and loaded, otherise false [bool]

	function load($UID) {
		$ary = dbLoad('projectrevisions', $UID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that object is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';
		$d = $this->data;

		if (strlen($d['UID']) < 5) { $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'projectrevisions';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'refUID' => 'VARCHAR(30)',		
			'content' => 'TEXT',
			'type' => 'VARCHAR(50)',
			'reason' => 'VARCHAR(255)',
			'editedBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME' );

		$dbSchema['indices'] = array('UID' => '10', 'refUID' => '10');

		$dbSchema['nodiff'] = array( 'UID', 'refUID', 'content', 'type', 'reason', 
									 'editedBy', 'editedOn', 'recordAlias' );
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
		$ary = $this->data;
		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (authHas('wiki', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%wiki/' . $ary['recordAlias'];
			$ary['viewLink'] = "<a href='%%serverPath%%wiki/" . $ary['recordAlias'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------
		$ary['editedOnLong'] = date('jS F, Y', strtotime($ary['editedOn']));

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete the current revision and all its assets
	//----------------------------------------------------------------------------------------------

	function delete() {
		if (dbRecordExists('projectrevisions', $this->data['UID']) == false) { return false; }
		dbDelete('projectrevisions', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
		$report = "<h3>Installing Project Revisions</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create project revisions table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('projectrevisions') == false) {	
			echo "crating table: projectrevisions\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created projectrevisions table and indices...<br/>';
		} else {
			$this->report .= 'projectrevisions table already exists...<br/>';	
		}
		return $report;
	}

}

?>
