<?

//--------------------------------------------------------------------------------------------------
//	index table
//--------------------------------------------------------------------------------------------------
//	project members can all edit the project, but only the person who intiated the project may 
//	add members.

//	Member role may be:
//	 admin (can add members, edit project)
//	 member (can edit project)

class ProjectMembership {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function ProjectMembership($projectUID = '', $userUID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if (($projectUID != '') AND ($userUID != '')) { $this->load($projectUID, $userUID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($projectUID, $userUID) {
		$sql = "select * from projectmembers "
			 . "where projectUID='" . sqlMarkup($projectUID) . "' "
			 . "and userUID='" . sqlMarkup($userUID) . "'";

		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) {
			$this->data = sqlRMArray(dbFetchAssoc($result));
			return true;
		}
		return false;
	}

	function loadArray($ary) { $this->data = $ary; }

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

		if (strlen($d['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }
		if (dbRecordExists('users', $this->data['userUID']) == false) 
			{ $verify .= "Member does not exist."; }
		if (dbRecordExists('projects', $this->data['projectUID']) == false) 
			{ 
				//echo "this->data[projectUID]: " . $this->data['projectUID'] . "<br/>\n";
				$verify .= "Project does not exist."; 
			}

		return $verify;
	}
	
	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'projectmembers';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'projectUID' => 'VARCHAR(30)',	
			'userUID' => 'VARCHAR(30)',
			'role' => 'VARCHAR(10)',
			'joined' => 'DATETIME',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)' );

		$dbSchema['indices'] = array('UID' => '10', 'projectUID' => '10', 'userUID' => '10');
		$dbSchema['nodiff'] = array('UID', 'recordAlias');
		return $dbSchema;

	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function delete() {
		dbDelete('projectmembers', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	install this model
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Project Memberships</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create project memberships table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('projectmembers') == false) {	
			echo "crating table: projectmembers\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created projectmembers table and indices...<br/>';
		} else {
			$this->report .= 'projectmembers table already exists...<br/>';	
		}
		return $report;
	}

}

?>
