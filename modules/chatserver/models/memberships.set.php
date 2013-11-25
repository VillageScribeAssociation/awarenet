<?

	require_once($kapenta->installPath . 'modules/chatserver/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of memberships of a chat room
//--------------------------------------------------------------------------------------------------

class Chatserver_Memberships {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;							//_	serialized chatserver_membership objects [array]
	var $loaded = false;					//_	set to true when members loaded [bool]

	var $roomUID = '';						//_	UID of Chatserver_Room object [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: roomUID - UID of a Chatserver_Room [string]
	//opt: load - set to false to use lazy initialization

	function Chatserver_Memberships($roomUID = '', $load = true) {
		$this->roomUID = $roomUID;
		if (('' != $this->roomUID) && (true == $load)) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of memberships to this room
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;	
		if ('' == $this->roomUID) { return false; }

		$this->members = array();
		$conditions = array("room='" . $db->addMarkup($this->roomUID) . "'");
		$range = $db->loadRange('chatserver_membership', '*', $conditions, 'user ASC');
		if (false === $range) { return false; }

		foreach($range as $item) { $this->members[$item['user']] = $item; }
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a room has a given member (user UID)
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if found, false if not [bool]

	function has($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		if (true == array_key_exists($userUID, $this->members)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a new membership in the current room
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//arg: role - role within this room (admin|member|banned) [string]
	//returns: true on success, false on failure [bool]

	function add($userUID, $role) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		if (('admin' != $role) && ('member' != $role) && ('banned' != $role)) { return false; }

		$this->remove($userUID);					// first clear any existing membership

		$model = new Chatserver_Membership();
		$model->user = $userUID;
		$model->room = $this->roomUID;
		$model->role = $role;
		$report = $model->save();

		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove any membership(s) for the given user in the current room
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if membership removed, false if not [bool]

	function remove($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		$deleted = false;
		
		foreach($this->members as $item) {
			if ($item['user'] == $userUID) {
				$model = new Chatserver_Membership($item['UID']);
				$check = $model->delete();
				if (true == $check) { $deleted = true; }
			}
		}

		return $deleted;
	}

	//----------------------------------------------------------------------------------------------
	//.	count memebrs of this room
	//----------------------------------------------------------------------------------------------
	//returns: number of members [int]

	function count() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return 0; }	
		return count($this->members);
	}

	//----------------------------------------------------------------------------------------------
	//.	convert this to an XML fragment
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent xml fragment by [string]
	//returns: xml fragment representing all memberships of this room [string]

	function toXml($indent = '') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$xml = $indent . "<memberships>\n";				//%	return value [string]

		foreach ($this->members as $item) {
			$xml .= ''
			 . $indent . "\t<member>"
			 . "<user>" . $item['user'] . "</user>"
			 . "<role>" . $item['role'] . "</role>"
			 . "</member>\n";
		}	
	
		$xml .= $indent . "</memberships>\n";		

		return $xml;
	}

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	rm - room membership hash
	//----------------------------------------------------------------------------------------------
	//returns: hash of all memberships, or emp[ty string on failure [string]

	function rm() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		$txt = '';

		foreach($this->members as $item) { $txt .= $item['user'] . "|" . $item['role'] . '|'; }
		$hash = sha1($txt);
		//$this->set('rm-' . $roomUID, $hash);		//TODO: caching
		return $hash;
	}

}

?>
