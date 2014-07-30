<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of all chat rooms
//--------------------------------------------------------------------------------------------------

class Chat_Rooms {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	array of serialized Chat_Room objects [array]
	var $loaded = false;		//_	set to true when memebrs loaded [bool]

	var $hasLocal = false;			//_	set to true if local rooms exist [string]

	var $lastError = '';

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: load - load room data immediately, set to false for lazy initialization [bool]

	function Chat_Rooms($load = true) {
		$this->members = array();
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load all chat rooms from local table
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;
		$this->members = array();
		$this->hasLocal = false;
		$conditions = array();		//%	add any conditions here [array]

		$range = $db->loadRange('chat_room', '*', $conditions, 'UID ASC');
		if (false === $range) { return false; }

		foreach($range as $item) {
			$this->members[$item['UID']] = $item;
			if ('local' == $item['state']) { $this->hasLocal = true; }
		}
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a given room (UID) exists in local table
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Chat_Room object [string]
	//returns: true if found in local set, false if not [string]

	function has($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		if (true == array_key_exists($UID, $this->members)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover state (local|global) of a Chat_Room object [string]
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Chat_Room object [string]
	//returns: state string or empty string on failure (local|global) [string]

	function getState($UID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		if (false == array_key_exists($UID, $this->members)) { return ''; }
		return $this->members[$UID]['state'];
	}

	//==============================================================================================
	//	XML Room lists and Chatserver I/O
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	check and XML room list from the server against local rooms and memberships
	//----------------------------------------------------------------------------------------------
	//arg: rlXml - xml room list fragment [string]

	function checkListXml($rlXml) {
		echo "Checking room list:<br/><textarea rows='10' cols='80'>$rlXml</textarea><br/>\n";

		$xd = new KXmlDocument($rlXml);
		$children = $xd->getChildren();		//%	handles to children of root node [array]
		$rooms = array();					//%	UID of all global rooms [array:string]

		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			//echo "childId: $childId (" . $child['type'] . ")<br/>\n";
			if ('room' == $child['type']) {
				//----------------------------------------------------------------------------------
				//	read 'room' set
				//----------------------------------------------------------------------------------
				$keys = $xd->getChildren($childId);
				$uid = '';					//%	UID of a room known to the server [string]
				$rh = '';					//%	chat room hash [string]
				$rm = '';					//%	room membership hash [string]

				foreach($keys as $keyId) {
					$entity = $xd->getEntity($keyId);
					switch($entity['type']) {
						case 'uid':		$uid = $entity['value'];		break;
						case 'rh':		$rh = $entity['value'];			break;
						case 'rm':		$rm = $entity['value'];			break;
						default: echo "*** unknown type: $type<br/>\n";	break;
					}
				}

				if ('' != $uid) { $rooms[] = $uid; }

				//----------------------------------------------------------------------------------
				//	get the room if unknown
				//----------------------------------------------------------------------------------	
				if (('' != $uid) && (false == $this->has($uid))) {
					echo "*** importing room: $uid<br/>";
					$check = $this->importRoomXml($uid);
					if (true == $check) { echo "*** new room imported $uid<br/>"; }
					else { echo "*** could not import new room $uid<br/>"; }
				} else {
					echo "*** known room $uid<br/>\n";
				}

				//----------------------------------------------------------------------------------
				//	update the room if rh mismatch
				//----------------------------------------------------------------------------------	
				if (('' != $rh) && ('' != $uid) && ($rh != $this->rh($uid))) {
					echo "*** room hash mismatch: $uid <br/>\n";
					$check = $this->importRoomXml($uid);
					if (true == $check) { echo "*** room updated $uid<br/>"; }
					else { echo "*** could not update room $uid<br/>"; }
				} else {
					echo "*** room hash confirmed<br/>\n";
				}

				//----------------------------------------------------------------------------------
				//	update the membership list if rm mismatch
				//----------------------------------------------------------------------------------	
				if (('' != $rm) && ('' != $uid) && ($rm != $this->rm($uid))) {
					echo "*** membership hashes mismatch $rm != " . $this->rm($uid) . "<br/>\n";
					$check = $this->importMembershipsXml($uid);
					if (true == $check) { echo "*** room members updated $uid<br/>"; }
					else { echo "*** could not update room members for $uid<br/>"; }

				} else {
					echo "*** membership hashes match<br/>\n";
				}

				//----------------------------------------------------------------------------------
				//	check that the room state is global
				//----------------------------------------------------------------------------------	
				if (('' != $uid) && (true == $this->has($uid))) {
					$objAry = $this->members[$uid];
					if ('global' != $objAry['state']) {
						$model = new Chat_Room($uid);
						if (true == $model->loaded) {
							$model->state = 'global';
							$report = $model->save();
							if ('' == $report) { echo "*** made room $uid global<br/>"; }
							else { echo "*** could not make room global: $uid<br/>"; }
						}
					}
				}

			}
		}

		//------------------------------------------------------------------------------------------
		//	make local any global rooms not reported by the server
		//------------------------------------------------------------------------------------------
		foreach ($this->members as $item) {
			if (('global' == $item['state']) && (false == in_array($item['UID'], $rooms))) {
				$model = new Chat_Room($item['UID']);
				$model->state = 'inactive';
				$report = $model->save();
				if ('' == $report) { echo "*** room " . $model->UID . " is now deleted<br/>"; }
				else { echo "*** could not set local " . $model->UID . ": $report<br/>"; }
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	get chat room details from central server and save to database
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID oc a Chatserver_Room object [string]
	//returns: true on success, false on failure [bool]

	function importRoomXml($UID) {
		$io = new Chat_IO();
		$response = $io->send('getroom', $UID, '');
		echo "response:<br/><textarea rows='10' cols='80'>$response</textarea><br/>\n";

		//------------------------------------------------------------------------------------------
		//	save
		//------------------------------------------------------------------------------------------
		$model = new Chat_Room();
		$check = $model->loadXml($response);
		if (false == $check) { return false; }

		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	get chat room membership details from central server and save to database
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Chat_Room object [string]
	//returns: true on success, false on failure [bool]

	function importMembershipsXml($UID) {
		$set = new Chat_Memberships($UID, true);
		$check = $set->ImportMembershipsXml();
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	creates an XML fragment to describe new local rooms to server
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: xml fragment, or empty string on failure [string]

	function localRoomsXml($indent = '') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		if (false == $this->hasLocal) { return ''; }

		$xml = $indent . "<rn>\n";
		foreach($this->members as $item) {
			if ('local' == $item['state']) {
				$model = new Chat_Room($item['UID']);
				$xml .= $model->toXml($indent . "\t");
			}		
		}
		$xml .= $indent . "</rn>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	join a chat room
	//----------------------------------------------------------------------------------------------
	//;	note: this is done via a direct call to the server, rather than asynchronously
	//arg: roomUID - UID of a Chat_Room object [string]
	//arg: userUID - UID of a Users_User object [string]
	//arg: role - position in room (admin|member|banned) [string]
	//returns: true on success, false on failure [bool]

	function join($roomUID, $userUID, $role) {
		global $db;

		$this->lastError = '';

		if (false == $db->objectExists('users_user', $userUID)) { 
			$this->lastError = 'Unknown user.';
			return false;
		}
		if (false == $db->objectExists('chat_room', $roomUID)) {
			$this->lastError = 'Unknown chat room.';
			return false;
		}
		if (('admin' != $role) && ('member' != $role) && ('banned' != $role)) {
			//return false;
			$this->lastError = 'Unrecognized role in chat room.';
			return false;
		}

		$io = new Chat_IO();
		$msg = ''
		 . "<membership>\n"
		 . "\t<room>" . $roomUID . "</room>\n"
		 . "\t<user>" . $userUID . "</user>\n"
		 . "\t<role>" . $role . "</role>\n"
		 . "</membership>\n";

		echo "<h2>Request</h2>";
		echo "<textarea rows='10' style='width: 100%'>$msg</textarea><br/>\n";

		$response = $io->send('join', '', $msg);

		echo "<h2>Response</h2>";
		echo "<textarea rows='10' style='width: 100%'>$response</textarea><br/>\n";

		if (false === strpos($response, '<ok/>')) {
			$this->lastError = 'IO response failure.';
			return false;
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	leave / quit a chat room
	//----------------------------------------------------------------------------------------------
	//;	note: this is done via a direct call to the server, rather than asynchronously
	//arg: roomUID - UID of a Chat_Room object [string]
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function leave($roomUID, $userUID) {
		global $db;

		if (false == $db->objectExists('users_user', $userUID)) { return false; }
		if (false == $db->objectExists('chat_room', $roomUID)) { return false; }

		$io = new Chat_IO();
		$msg = ''
		 . "<membership>\n"
		 . "\t<room>" . $roomUID . "</room>\n"
		 . "\t<user>" . $userUID . "</user>\n"
		 . "</membership>\n";

		echo "<h2>Request</h2>";
		echo "<textarea rows='10' style='width: 100%'>$msg</textarea><br/>\n";

		$response = $io->send('leave', '', $msg);

		echo "<h2>Response</h2>";
		echo "<textarea rows='10' style='width: 100%'>$response</textarea><br/>\n";

		if (false === strpos($response, '<ok/>')) { return false; }
		return true;
	}

	//==============================================================================================
	//	HASHING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	ra - aggregate hash of all chat rooms and memberships
	//----------------------------------------------------------------------------------------------
	//returns: hash of all chat rooms and memberships, empty string on failure [string]

	function ra() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		$txt = '';
		foreach($this->members as $item) {
			if ('global' == $item['state']) {
				$roomHash = $this->rh($item['UID']);			// hash the room
				$membershipHash = $this->rm($item['UID']);		// hash the memberships
				$txt .= $roomHash . '|' . $membershipHash . '|';
			}
		}
		$hash = sha1($txt);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rh - room hash
	//----------------------------------------------------------------------------------------------
	//arg: roomUID - UID of a Chat_Room object [string]
	//returns: room hash, or empty string on failure [string]

	function rh($roomUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		if (false == $this->has($roomUID)) { return ''; }

		$txt = '';

		$model = new Chat_Room();
		$model->loadArray($this->members[$roomUID]);
		if (false == $model->loaded) { return ''; }

		$txt = $model->UID . '|' . $model->title . '|' . $model->description;
		$hash = sha1($txt);
		//$this->set('rh-' . $roomUID, $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rm - room membership hash
	//----------------------------------------------------------------------------------------------
	//arg: roomUID - UID of a Chat_Room object [string]

	function rm($roomUID) {
		$set = new Chat_Memberships($roomUID, true);
		$hash = $set->rm();
		return $hash;
	}

}

?>
