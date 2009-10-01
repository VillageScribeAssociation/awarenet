<?

//--------------------------------------------------------------------------------------------------
//	object for managing records of groups
//--------------------------------------------------------------------------------------------------
//	group type could be Team/Club/Society, etc

require_once($installPath . 'modules/groups/models/membership.mod.php');

class Group {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Group($UID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['name'] = 'New Group';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoadRa('groups', $uid);
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
		$this->data['recordAlias'] = raSetAlias('groups', $d['UID'], $d['name'], 'groups');
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
		$dbSchema['table'] = 'groups';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'school' => 'VARCHAR(30)',	
			'name' => 'VARCHAR(255)',
			'type' => 'VARCHAR(20)',
			'description' => 'TEXT',
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

		if (authHas('groups', 'view', $this->data)) { 
			$ary['viewUrl'] = '%%serverPath%%groups/' . $this->data['recordAlias'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (authHas('groups', 'edit', $this->data)) {
			$ary['editUrl'] =  '%%serverPath%%groups/edit/' . $this->data['recordAlias'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (authHas('groups', 'edit', $this->data)) {
			$ary['delUrl'] =  '%%serverPath%%groups/confirmdelete/UID_' . $this->data['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		if (authHas('groups', 'new', $this->data)) { 
			$ary['newUrl'] = "%%serverPath%%groups/new/"; 
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[add new group]</a>"; 
		}

		//------------------------------------------------------------------------------------------
		//	look up school info
		//------------------------------------------------------------------------------------------
	
		require_once($installPath . 'modules/schools/models/schools.mod.php');
		$mySchool = new School($this->data['school']);

		$ary['schoolName'] = $mySchool->data['name'];
		$ary['schoolCountry'] = $mySchool->data['country'];
		$ary['schoolRecordAlias'] = $mySchool->data['recordAlias'];
		$ary['schoolUrl'] = '%%serverPath%%/schools/' . $mySchool->data['recordAlias'];
		$ary['schoolLink'] = "<a href='" . $ary['schoolUrl'] . "'>" . $mySchool->data['name'] . "</a>";

		//------------------------------------------------------------------------------------------
		//	summary 
		//------------------------------------------------------------------------------------------

		$ary['contentHtml'] = str_replace(">\n", ">", $ary['description']);
		$ary['contentHtml'] = str_replace("\n", "<br/>\n", $ary['contentHtml']);
	
		$ary['summary'] = substr(strip_tags($ary['description']), 0, 400) . '...';

		//------------------------------------------------------------------------------------------
		//	marked up for wyswyg editor
		//------------------------------------------------------------------------------------------
		
		$ary['descriptionJs'] = $ary['description'];
		$ary['descriptionJs'] = str_replace("'", '--squote--', $ary['descriptionJs']);
		$ary['descriptionJs'] = str_replace("'", '--dquote--', $ary['descriptionJs']);
	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing groups Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create groups table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('groups') == false) {	
			echo "installing groups module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created groups table and indices...<br/>';
		} else {
			$this->report .= 'groups table already exists...<br/>';	
		}

		//------------------------------------------------------------------------------------------
		//	create group memberships table if it does not exist (index table with users)
		//------------------------------------------------------------------------------------------

		if (dbTableExists('groupmembers') == false) {	

			$dbSchema = array();
			$dbSchema['table'] = 'groupmembers';
			$dbSchema['fields'] = array(
				'UID' => 'VARCHAR(30)',	
				'groupUID' => 'VARCHAR(30)',	
				'userUID' => 'VARCHAR(30)',
				'position' => 'VARCHAR(20)',
				'admin' => 'VARCHAR(10)',
				'joined' => 'DATETIME' );

			$dbSchema['indices'] = array('UID' => '10', 'groupUID' => '10', 'userUID' => '10');
			$dbSchema['nodiff'] = array('UID', 'recordAlias');

			dbCreateTable($dbSchema);	
			$this->report .= 'created group meberships table and indices...<br/>';

		} else {
			$this->report .= 'group memberships table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {
		$sql = "delete from images where refModule='groups' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		$sql = "delete from files where refModule='groups' and refUID='" . $this->data['UID']. "'";
		dbQuery($sql);
		
		raDeleteAll('groups', $this->data['UID']);
		dbDelete('groups', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	load list of members
	//----------------------------------------------------------------------------------------------

	function getMembers() {
		$retVal = array();
		$sql = "select groupmembers.* from groupmembers, users "
			 . "where groupmembers.groupUID='" . $this->data['UID'] . "' "
			 . "and groupmembers.userUID = users.UID "
			 . "order by users.firstname";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) { $retVal[] = sqlRMArray($row); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	add a new member to the group
	//----------------------------------------------------------------------------------------------

	function addMember($userUID, $position, $admin) {
		$this->removemember($userUID);

		$model = new GroupMembership();
		$model->data['UID'] = createUID();
		$model->data['userUID'] = $userUID;
		$model->data['groupUID'] = $this->data['UID'];
		$model->data['position'] = $position;
		$model->data['admin'] = $admin;
		$model->data['joined'] = mysql_datetime();
		$model->save();
	}

	//----------------------------------------------------------------------------------------------
	//	remove a member from the group
	//----------------------------------------------------------------------------------------------

	function removeMember($userUID) {
		$sql = "delete from groupmembers "
			 . "where userUID='" . $userUID . "' and groupUID='" . $this->data['UID'] . "'";
		dbQuery($sql);
	}

	//----------------------------------------------------------------------------------------------
	//	determine if user can edit the group's membership /page
	//----------------------------------------------------------------------------------------------

	function hasEditAuth($userUID) {
		$model = new Users($userUID);
		if ($model->data['ofGroup'] == 'admin') { return true; }		
		if ($model->data['ofGroup'] == 'teacher') { return true; }

		$members = $this->getMembers();
		foreach($members as $row) { 
			if (($row['userUID'] == $userUID) && ('yes' == $row['admin'])) { return true; } 	
		}	
		return false;
	}

}

?>
