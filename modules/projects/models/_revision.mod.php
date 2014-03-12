<?

//--------------------------------------------------------------------------------------------------
//*	object to represent project revisions
//--------------------------------------------------------------------------------------------------
//+	since the projects module is derived from the wiki module, changes to wiki revisions model
//+	should be copied here.

class Projects_Revision {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $projectUID;		//_ ref_Projects_Project (varchar(33)) [string]
	var $sectionUID;		//_ ref:Projects_Section (varchar(33)) [string]
	var $title;				//_ title [string]
	var $content;			//_ plaintext [string]
	var $reason;			//_ varchar(255) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Revision object [string]

	function Projects_Revision($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }				// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Revision ' . $this->UID;	// set default title
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Revision object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Revision object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->projectUID = $ary['projectUID'];
		$this->sectionUID = $ary['sectionUID'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->reason = $ary['reason'];
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
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
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
		$dbSchema['module'] = 'projects';
		$dbSchema['model'] = 'projects_revision';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'projectUID' => 'VARCHAR(33)',
			'sectionUID' => 'VARCHAR(33)',
			'title' => 'VARCHAR(255)',
			'content' => 'TEXT',
			'reason' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'projectUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be not kept for any fields
		$dbSchema['nodiff'] = array(
			'UID', 'projectUID', 'sectionUID', 'title', 'content', 'reason',
			'createdOn', 'createdBy', 'editedOn', 'editedBy'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'projectUID' => $this->projectUID,
			'sectionUID' => $this->sectionUID,
			'title' => $this->title,
			'content' => $this->content,
			'reason' => $this->reason,
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
		global $kapenta;
		global $kapenta;

		$ary = $this->toArray();			//%	return value [dict]

		$ary['viewUrl'] = '';	$ary['viewLink'] = '';	// view

		//------------------------------------------------------------------------------------------
		//	load as project and convert to HTML
		//------------------------------------------------------------------------------------------		

		$model = new Projects_Project($ary['projectUID']);

		$ary['alias'] = $model->alias;
		$ary['status'] = $model->status;
		$ary['finishedOn'] = $model->finishedOn;

		$model->loadArray($ary);
		$ary = $model->extArray();

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $kapenta->user->authHas('projects', 'projects_revision', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%projects/showrevision/' . $ary['UID'];
			$ary['viewLink'] = "<a href='%%serverPath%%projects/showrevision/" . $ary['UID'] . "'>"
					 . "[read on &gt;&gt;]</a>"; 
		}	// TODO: action to view a single revision

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------
		$ary['editedOnLong'] = $kapenta->longDate($ary['editedOn']);

		//------------------------------------------------------------------------------------------
		//	done
		//------------------------------------------------------------------------------------------		
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

}

?>
