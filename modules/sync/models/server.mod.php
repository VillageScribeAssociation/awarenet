<?

//--------------------------------------------------------------------------------------------------
//*	object to represent server trust relationships
//--------------------------------------------------------------------------------------------------

class Sync_Server {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $servername;		//_ title [string]
	var $serverurl;			//_ varchar(100) [string]
	var $password;			//_ varchar(50) [string]
	var $direction;			//_ varchar(30) [string]
	var $active;			//_ varchar(10) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Server object [string]

	function Sync_Server($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->servername = 'New Server ' . $this->UID;		// set default servername
			$this->loaded = false;
		}
	}


	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Server object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Server object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $db;
		//if (false == $db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->servername = $ary['servername'];
		$this->serverurl = $ary['serverurl'];
		$this->password = $ary['password'];
		$this->direction = $ary['direction'];
		$this->active = $ary['active'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
		return true;
	}
	
	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db, $aliases;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'sync';
		$dbSchema['model'] = 'Sync_Server';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'servername' => 'VARCHAR(255)',
			'serverurl' => 'VARCHAR(100)',
			'password' => 'VARCHAR(50)',
			'direction' => 'VARCHAR(30)',
			'active' => 'VARCHAR(10)',
			'publickey' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will not be kept for these fields
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
			'servername' => $this->servername,
			'serverurl' => $this->serverurl,
			'password' => $this->password,
			'direction' => $this->direction,
			'active' => $this->active,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		global $user;
		$ary = $this->toArray();	
		
		$ary['editUrl'] = '';
		$ary['editLink'] = '';
		$ary['viewUrl'] = '';
		$ary['viewLink'] = '';
		$ary['delUrl'] = '';
		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('sync', 'Sync_Server', 'view', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%sync/server/' . $this->alias;
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[read on &gt;&gt;]</a>"; 
		}

		if (true == $user->authHas('sync', 'Sync_Server', 'edit', $this->UID)) {
			$ary['editUrl'] =  '%%serverPath%%sync/editserver/' . $this->alias;
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
		}

		if (true == $user->authHas('sync', 'Sync_Server', 'edit', $this->UID)) {
			$ary['delUrl'] =  '%%serverPath%%sync/delserver/' . $this->UID . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete]</a>"; 
		}
		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}
	
}
?>
