<?

	require_once(dirname(__FILE__) . '/friendship.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object helper object representing a user's friendships
//--------------------------------------------------------------------------------------------------
//+	Note: lazy initialization - this object does not load the set of friendships until they are 
//+	needed for something.  This can be done using the load() method if you must.

class Users_Friendships {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	array of serialized User_Friendship obejcts [string]
	var $loaded = false;			//_	set to true when set of friendships is loaded [bool
	var $userUID = '';				//_	UID of the user this set belongs to [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: userUID - UID of a Users_User object [string]	

	function Users_Friendships($userUID = '') {
		$this->members = array();
		$this->userUID = $userUID;				
	}

	//----------------------------------------------------------------------------------------------
	//.	load a user's friends, friend requests, etc
	//----------------------------------------------------------------------------------------------
	//returns: true on sucess, false on failure [bool]

	function load() {
		global $kapenta;
		if ('' == $this->userUID) { return false; }
		$UID = $kapenta->db->addMarkup($this->userUID);
		$conditions = array();
		$conditions[] = "(userUID='" . $UID . "' or friendUID='" . $UID . "')";
		
		$this->members = $kapenta->db->loadRange('users_friendship', '*', $conditions);		
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if this->userUID has a confirmed friendship with another user
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [string]

	function hasConfirmed($friendUID) {
		if ('' == $this->userUID) { return false; }
		if (false == $this->loaded) { $this->load(); }

		foreach($this->members as $item) {
			if (($item['friendUID'] == $friendUID) && ('confirmed' == $item['status'])) {
				return true;
			}
		}
		
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	discover if this->userUID has sent a friend request to another user
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [string]

	function hasUnconfirmed($friendUID) {
		if ('' == $this->userUID) { return false; }
		if (false == $this->loaded) { $this->load(); }

		foreach($this->members as $item) {
			if (($item['friendUID'] == $friendUID) && ('unconfirmed' == $item['status'])) {
				return true;
			}
		}
		
		return false;
	}
	//----------------------------------------------------------------------------------------------
	//.	get the current users friends
	//----------------------------------------------------------------------------------------------
	//returns: array of friendshipUID => friendship for loadArray [array]
	//, TODO: handle this differently, perhaps with a block
	//, TODO: see if this can be removed entirely

	function getConfirmed() {
		$range = array();								//%	return value [array]
		if ('' == $this->userUID) { return $range; }
		if (false == $this->loaded) { $this->load(); }

		foreach($this->members as $item) {
			if (($this->userUID == $item['userUID']) && ('confirmed' == $item['status'])) {
				$range[] = $item;
			}
		}

		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	get inbound friend requests (that have been made to this user)
	//----------------------------------------------------------------------------------------------

	function getRequestsByMe() {
		$range = array();								//%	return value [array]
		if ('' == $this->userUID) { return $range; }
		if (false == $this->loaded) { $this->load(); }

		foreach($this->members as $item) {
			if (($this->userUID == $item['userUID']) && ('unconfirmed' == $item['status'])) {
				$range[] = $item;
			}
		}

		return $range;
	}

	//----------------------------------------------------------------------------------------------
	//.	get outbound friend requests (that this user has made) 
	//----------------------------------------------------------------------------------------------
	
	function getRequestsOfMe() {
		$range = array();								//%	return value [array]
		if ('' == $this->userUID) { return $range; }
		if (false == $this->loaded) { $this->load(); }

		foreach($this->members as $item) {
			if (($this->userUID == $item['friendUID']) && ('unconfirmed' == $item['status'])) {
				$range[] = $item;
			}
		}

		return $range;
	}
}

?>
