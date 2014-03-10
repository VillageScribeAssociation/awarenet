<?

//--------------------------------------------------------------------------------------------------
//*	object for personal messages, like webmail
//--------------------------------------------------------------------------------------------------

class Messages_Message {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $owner;				//_ varchar(33) [string]
	var $folder;			//_ varchar(33) [string]
	var $fromUID;			//_ varchar(33) [string]
	var $fromName;			//_	varchar(255) [string]
	var $toUID;				//_ varchar(33) [string]
	var $toName;			//_	varchar(255) [string]
	var $cc;				//_ text [string]
	var $title;				//_ title [string]
	var $content;			//_ wyswyg [string]
	var $status;			//_ varchar(10) [string]
	var $re;				//_ varchar(33) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Message object [string]

	function Messages_Message($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $UID) { $this->load($UID); }			// try load an object from the database
		if (false == $this->loaded) {						// check if we did
			$this->data = $kapenta->db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->title = 'New Message ' . $this->UID;		// set default title
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Message object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if (false === $objary) { return false; }
		if (false == $this->loadArray($objary)) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. load Message object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->owner = $ary['owner'];
		$this->folder = $ary['folder'];
		$this->fromUID = $ary['fromUID'];
		$this->fromName = $ary['fromName'];
		$this->toUID = $ary['toUID'];
		$this->toName = $ary['toName'];
		$this->cc = $ary['cc'];
		$this->title = $ary['title'];
		$this->content = $ary['content'];
		$this->status = $ary['status'];
		$this->re = $ary['re'];
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
		$dbSchema['module'] = 'messages';
		$dbSchema['model'] = 'messages_message';
		$dbSchema['archive'] = 'yes';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'owner' => 'VARCHAR(33)',
			'folder' => 'VARCHAR(33)',
			'fromUID' => 'VARCHAR(33)',
			'fromName' => 'VARCHAR(255)',
			'toUID' => 'VARCHAR(33)',
			'toName' => 'VARCHAR(255)',
			'cc' => 'TEXT',
			'title' => 'VARCHAR(255)',
			're' => 'VARCHAR(33)',
			'content' => 'TEXT',
			'status' => 'VARCHAR(10)',
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

		//revision history will be kept for these fields
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
			'owner' => $this->owner,
			'folder' => $this->folder,
			'fromUID' => $this->fromUID,
			'fromName' => $this->fromName,
			'toUID' => $this->toUID,
			'toName' => $this->toName,
			'cc' => $this->cc,
			'title' => $this->title,
			'content' => $this->content,
			'status' => $this->status,
			're' => $this->re,
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
		global $user;

		$ary = $this->toArray();		//%	return value [dict]

		$ary['editUrl'] = '';		$ary['editLink'] = '';
		$ary['viewUrl'] = '';		$ary['viewLink'] = '';
		$ary['newUrl'] = '';		$ary['newLink'] = '';
		$ary['delUrl'] = '';		$ary['delLink'] = '';

		//------------------------------------------------------------------------------------------
		//	check authorisation
		//------------------------------------------------------------------------------------------

		$auth = false;
		if ('admin' == $user->role) { $auth = true; }
		if ($user->UID == $ary['createdBy']) { $auth = true; }

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------

		if (true == $user->authHas('messages', 'messages_message', 'show', $this->UID)) { 
			$ary['viewUrl'] = '%%serverPath%%messages/' . $ary['UID'];
			$ary['viewLink'] = "<a href='" . $ary['viewUrl'] . "'>[see all threads &gt;&gt;]</a>"; 
		}

		if ($auth == true) {
			$ary['editUrl'] =  '%%serverPath%%messages/edit/' . $ary['UID'];
			$ary['editLink'] = "<a href='" . $ary['editUrl'] . "'>[edit]</a>"; 
			$ary['newUrl'] = "%%serverPath%%messages/new/";
			$ary['newLink'] = "<a href='" . $ary['newUrl'] . "'>[create new messages]</a>";  
			$ary['addChildUrl'] = "%%serverPath%%messages/addchild/" . $ary['UID'];
			$ary['addChildLink'] = "<a href='" . $ary['addChildUrl'] . "'>[add child messages]</a>";  
			$ary['delUrl'] = "%%serverPath%%messages/confirmdelete/UID_" . $ary['UID'] . '/';
			$ary['delLink'] = "<a href='" . $ary['delUrl'] . "'>[delete messages]</a>";  
		}

		//------------------------------------------------------------------------------------------
		//	strandardise date format to previous website
		//------------------------------------------------------------------------------------------

		$ary['longdate'] = $kapenta->longDate($ary['createdOn']);
		$ary['titleUpper'] = strtoupper($ary['title']);

		//------------------------------------------------------------------------------------------
		//	redundant - namespace issue
		//------------------------------------------------------------------------------------------

		$ary['messageTitle'] = $ary['title'];

		//------------------------------------------------------------------------------------------
		//	look up user
		//------------------------------------------------------------------------------------------

		$model = new Users_User($ary['createdBy']);
		$ary['userName'] = $model->firstname . ' ' . $model->surname;		
		$ary['userRa'] = $model->alias;
		$ary['userUrl'] = '%%serverPath%%users/profile/' . $ary['userRa'];
		$ary['userLink'] = "<a href='" . $ary['userUrl'] . "'>" . $ary['userRa'] . "</a>";
	
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
