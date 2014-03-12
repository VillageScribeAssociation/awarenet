<?

//--------------------------------------------------------------------------------------------------
//*	object to represent user friendships
//--------------------------------------------------------------------------------------------------
//+	friendships act in a single direction only and only show up when requited.  status can be 
//+	confirmed or unconfirmed.  the relationship it chosen at each end, eg, Alice is Bobs 'sister'
//+	and Bob is Alices 'brother'.  Let the soap opera begin.

class Users_Friendship {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;					// currently loaded record [array]
	var $dbSchema;				// database structure [array]
	var $loaded = false;		// set to true when an object has been loaded

	var $UID;					//_ UID [string]
	var $userUID;				//_ ref:Users_User [string]
	var $friendUID;				//_ ref:Users_User [string]
	var $relationship;			//_ varchar(100) [string]
	var $status;				//_ varchar(255) [string]
	var $createdOn;				//_ datetime [string]
	var $createdBy;				//_ ref:Users_User [string]
	var $editedOn;				//_ datetime [string]
	var $editedBy;				//_ ref:Users_User [string]
	
	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID fo a friendship object [string]

	function Users_Friendship($UID = '') {
		global $kapenta;
		global $kapenta;

		$this->dbSchema = $this->getDbSchema();
		if ($UID != '') { $this->load($UID); }
		if (false == $this->loaded) {
			$this->data = $kapenta->db->makeBlank($this->dbSchema);
			$this->loadArray($this->data);
			$this->userUID = $kapenta->user->UID;
			$this->status = 'unconfirmed';
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load a friendship object given a user's UID and their friend's UID
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: friendUID - UID of a user [string]
	//returns: true on success, false if not found [bool]

	function loadFriend($userUID, $friendUID) {
		global $kapenta;

		$conditions = array();
		$conditions[] = "userUID='" . $kapenta->db->addMarkup($userUID) . "'";
		$conditions[] = "friendUID='" . $kapenta->db->addMarkup($friendUID) . "'";
		//$conditions[] = "status='confirmed'";

		$range = $kapenta->db->loadRange('users_friendship', '*', $conditions, '', '', '');

		if (count($range) > 0) { 
			$this->loadArray(array_pop($range)); 
			return true; 

		} else { return false; }
	}
	
	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Friendship object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Friendship object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;
		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }
		$this->UID = $ary['UID'];
		$this->userUID = $ary['userUID'];
		$this->friendUID = $ary['friendUID'];
		$this->relationship = $ary['relationship'];
		$this->status = $ary['status'];
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
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if (strlen($this->UID) < 5) { $report .= "UID not present.\n"; }
		if (strlen($this->userUID) < 5) { $report .= "user UID not present.\n"; }
		if (strlen($this->friendUID) < 5) { $report .= "friend UID not present.\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'users_friendship';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'userUID' => 'VARCHAR(33)',
			'friendUID' => 'VARCHAR(33)',
			'relationship' => 'VARCHAR(100)',
			'status' => 'VARCHAR(255)',
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
			'userUID' => $this->userUID,
			'friendUID' => $this->friendUID,
			'relationship' => $this->relationship,
			'status' => $this->status,
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
	//returns: associative array of member variables and metadata [array]

	function extArray() {
		global $kapenta;
		$ary = $this->toArray();
		// TODO	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.php

	function install() {
		global $kapenta;
		$report = "<h3>Installing Friendships Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create friendships table if it does not exist
		//------------------------------------------------------------------------------------------

		if ($kapenta->db->tableExists('friendships') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created friendships table and indices...<br/>';
		} else {
			$this->report .= 'friendships table already exists...<br/>';	
		}

		return $report;
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

	//----------------------------------------------------------------------------------------------
	//.	find common friends between two users // TODO: improve this
	//----------------------------------------------------------------------------------------------
	//arg: user1 - UID of a user [string]
	//arg: user2 - UID of a user [string]
	//returns: array of user UIDs [array]

	function findCommonFriends($user1, $user2) {
		$retVal = array();
		$friends1 = $this->getFriends($user1);
		$friends2 = $this->getFriends($user2);

		foreach($friends1 as $UID => $relationship) {
			if (array_key_exists($UID, $friends2) == true) { $retVal[] = $UID; }
		}

		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//.	find out if a friendship OR friend request exists
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user [string]
	//arg: friendUID - UID of a user [string]
	//returns: true if a link exists, false if one does not [bool]

	function linkExists($userUID, $friendUID) {
		global $kapenta;
		$conditions = array();
		$conditions[] = "userUID='" . $kapenta->db->addMarkup($userUID) . "'";
		$conditions[] = "friendUID='" . $kapenta->db->addMarkup($friendUID) . "'";

		$result = $kapenta->db->loadRange('users_friendship', '*', $conditions, '', '', '');
		if (count($result) > 0) { return true; }
		return false;
	}	

}

?>
