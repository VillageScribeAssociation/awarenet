<?

//--------------------------------------------------------------------------------------------------
//	object for manging wiki revisions
//--------------------------------------------------------------------------------------------------

class ProjectRevision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database table structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function ProjectRevision($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($UID) {
		$ary = dbLoad('projectrevisions', $UID);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() {
		return $this->data;
	}

	//----------------------------------------------------------------------------------------------
	//	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

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
	//	delete a revision and all its assets
	//----------------------------------------------------------------------------------------------

	function delete() {
		if (dbRecordExists('projectrevisions', $this->data['UID']) == false) { return false; }
		dbDelete('projectrevisions', $this->data['UID']);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	install this model
	//----------------------------------------------------------------------------------------------

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
