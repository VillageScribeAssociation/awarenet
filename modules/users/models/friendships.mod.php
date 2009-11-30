<?

//--------------------------------------------------------------------------------------------------
//	friendships
//--------------------------------------------------------------------------------------------------
//	friendships act in a single direction only and only show up when requited.  status can be 
//  confirmed or unconfirmed.  the relationship it chosen at each end, eg, Alice is Bobs 'sister'
//  and Bob is Alices 'brother'.  Let the soap opera begin.

class Friendship {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function Friendship($UID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['userUID'] = $user->data['UID'];
		$this->data['status'] = 'unconfirmed';
		if ($UID != '') { $this->load($UID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('friendships', $uid);
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

		if (strlen($this->data['UID']) < 5) { $verify .= "UID not present.\n"; }
		if (strlen($this->data['userUID']) < 5) { $verify .= "user UID not present.\n"; }
		if (strlen($this->data['friendUID']) < 5) { $verify .= "friend UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'friendships';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'userUID' => 'VARCHAR(30)',
			'friendUID' => 'VARCHAR(30)',
			'relationship' => 'VARCHAR(100)',
			'status' => 'VARCHAR(255)',		
			'createdOn' => 'DATETIME',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)' );

		$dbSchema['indices'] = array(
			'UID' => '10', 
			'userUID' => '10', 
			'friendUID' => '10', 
			'status' => '5');

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
		global $user;
		$ary = $this->data;
		// TODO	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Friendships Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create friendships table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('friendships') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created friendships table and indices...<br/>';
		} else {
			$this->report .= 'friendships table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {	dbDelete('friendships', $this->data['UID']); }

	//----------------------------------------------------------------------------------------------
	//	return a users list of friends [friendUID] => [relationship]
	//----------------------------------------------------------------------------------------------

	function getFriends($userUID) {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where userUID='" . sqlMarkup($userUID) . "' "
			 . "and status='confirmed' "
			 . "order by createdOn DESC";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$retVal[$row['friendUID']] = $row['relationship'];
		}
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	return a users list of friend requests (they have made) [friendUID] => [relationship]
	//----------------------------------------------------------------------------------------------

	function getFriendRequests($userUID) {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where userUID='" . sqlMarkup($userUID) . "' "
			 . "and status='unconfirmed' "
			 . "order by createdOn DESC";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$retVal[$row['friendUID']] = $row['relationship'];
		}
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	return a users list of friend requests (others have made) [friendUID] => [relationship]
	//----------------------------------------------------------------------------------------------

	function getFriendRequested($userUID) {
		$retVal = array();
		$sql = "select * from friendships "
			 . "where friendUID='" . sqlMarkup($userUID) . "' "
			 . "and status='unconfirmed' "
			 . "order by createdOn DESC";

		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$retVal[$row['userUID']] = $row['relationship'];
		}
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//	find common friends between two users // TODO: improve this
	//----------------------------------------------------------------------------------------------

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
	//	find out if a friendship OR friend request exists
	//----------------------------------------------------------------------------------------------

	function linkExists($userUID, $friendUID) {
		$sql = "select * from friendships "
			 . "where userUID='" . $userUID . "' "
			 . "and friendUID='" . sqlMarkup($friendUID) . "'";

		$result = dbQuery($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;
	}

}

?>
