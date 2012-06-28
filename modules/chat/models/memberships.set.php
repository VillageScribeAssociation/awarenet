<?

	require_once($kapenta->installPath . 'modules/chat/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object representing the set of memebrs in a chat room
//--------------------------------------------------------------------------------------------------

class Chat_Memberships {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $members;					//_	array serialized Chat_membership obejcts [array]
	var $loaded = false;			//_	set to true when memberships loaded [bool]
	var $roomUID = '';				//_	UID of a Chat_Room object [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: roomUID - UID of a Chat_Room object whose members to load [string]
	//opt: load - set to false to use lazy initialization [bool]

	function Chat_Memberships($roomUID = '', $load = true) {
		$this->members = array();
		$this->roomUID = $roomUID;
		if (('' != $roomUID) && (true == $load)) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a set of memberships from the database
	//----------------------------------------------------------------------------------------------
	//arg: room - UID of a Chat_Room object [string]

	function load() {
		global $db;

		if ('' == $this->roomUID) { return false; }
		if (false == $db->objectExists('chat_room', $this->roomUID)) { return false; }

		$conditions = array("room='" . $db->addMarkup($this->roomUID) . "'");
		$range = $db->loadRange('chat_membership', '*', $conditions, 'user');
		$this->members = $range;
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a user is a mamber of this chat room
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if user is a memebr of this room, false if not found [bool]

	function has($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		foreach($this->members as $item) {
			if ($userUID == $item['user']) { return true; }
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a user to the chat room (memebrship local to this instance)
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//arg: role - role within room (admin|member|banned) [string]
	//opt: state - default is local (local|global) [string
	//returns: true on success, false on failure [bool]

	function add($userUID, $role, $state = 'local') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		//TODO: check userUID, role, $this->roomUID

		if (true == $this->has($userUID)) {
			//--------------------------------------------------------------------------------------
			//	change role if user is already a member
			//--------------------------------------------------------------------------------------
			foreach($this->members as $item) {
				if ($userUID == $item['user']) {
					$model = new Chat_Membership($item['UID']);
					$model->role = $role;
					$model->state = $state;
					$report = $model->save();
					if ('' == $report) { return true; }
					return false;
				}
			}
		} else {
			//--------------------------------------------------------------------------------------
			//	add new member (client will sync it into a global membership)
			//--------------------------------------------------------------------------------------
			$model = new Chat_Membership();
			$model->room = $this->roomUID;
			$model->user = $userUID;
			$model->role = $role;
			$model->state = $state;
			$report = $model->save();
			if ('' == $report) { return true; }
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a user from this chat room
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if removed, false if not [bool]

	function remove($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		if (false == $this->has($userUID)) { return false; }
		foreach($this->members as $item) {
			if ($userUID == $item['user']) {
				$model = new Chat_membership($item['UID']);
				$check = $model->delete();
				$this->load($this->roomUID);					// reload memebrshiip list
				return $check;
			}
		}
		return false;			// unreachable?
	}

	//----------------------------------------------------------------------------------------------
	//.	count members in this room
	//----------------------------------------------------------------------------------------------
	//returns: number of members known to be in this room [string]

	function count() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return 0; }
		return count($this->members);
	}

	//==============================================================================================
	//	CHATSERVER I/O
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	import a set of memberships from central server
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function importMembershipsXml() {
		$allOk = true;						//%	return value [bool]	
		$known = array();					//%	userUID => role [array]

		//------------------------------------------------------------------------------------------
		//	query central chat server
		//------------------------------------------------------------------------------------------
		$io = new Chat_IO();
		$response = $io->send('getmemberships', $this->roomUID, '');
		echo "response:<br/><textarea rows='10' cols='80'>$response</textarea><br/>\n";

		//------------------------------------------------------------------------------------------
		//	parse XML response
		//------------------------------------------------------------------------------------------
		$xd = new KXmlDocument($response);
		$root = $xd->getEntity(1);
		if ('memberships' != $root['type']) { return false; }

		$children = $xd->getChildren();
		foreach($children as $childId) {
			//------------------------------------------------------------------------------------------
			//	extract individual memberships
			//------------------------------------------------------------------------------------------
			$child = $xd->getEntity($childId);
			if ('member' == $child['type']) {
				$parts = $xd->getChildren($childId);
				$userUID = '';
				$chatrole = '';

				foreach($parts as $partId) {
					$part = $xd->getEntity($partId);
					if ('user' == $part['type']) { $userUID = $part['value']; }
					if ('role' == $part['type']) { $chatrole = $part['value']; }
				}

				if (('' != $userUID) && ('' != $chatrole)) {
					$known[$userUID] = $chatrole;
					$check = $this->add($userUID, $chatrole, 'global');
					if (true == $check) { echo "*** added membersip $userUID $chatrole<br/>\n"; }
					else {
						$allOk = false;
						echo "*** could not add membership $userUID $chatrole <br/>\n";
					}
				} else {
					echo "*** XML error, missing required field<br/>\n";
					$allOk = false;
				}

			}
		}
		
		//------------------------------------------------------------------------------------------
		//	reload the list and remove memberships not reported by central server
		//------------------------------------------------------------------------------------------
		$this->load();
		foreach($this->members as $item) {
			if (false == array_key_exists($item['user'], $known)) {
				$check = $this->remove($item['user']);
				if (false == $check) { $allOk = false; }
				else { echo "** removed membership for " . $item['user'] . "<br/>\n"; }
			}
		}						

		return $allOk;
	}

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	rm - room membership hash
	//----------------------------------------------------------------------------------------------
	//arg: roomUID - UID of a Chat_Room object [string]

	function rm() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		$txt = '';

		foreach($this->members as $item) {
			if ('global' == $item['state']) {
				$txt .= $item['user'] . "|" . $item['role'] . '|';
			}
		}
		$hash = sha1($txt);
		//$this->set('rm-' . $roomUID, $hash);		//TODO: caching
		return $hash;
	}

}

?>
