<?

	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of all chat rooms
//--------------------------------------------------------------------------------------------------

class Chatserver_Rooms {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	array of serialized Chat_Room objects [array]
	var $loaded = false;		//_	set to true when memebrs loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: load - load room data immediately, set to false for lazy initialization [bool]

	function Chatserver_Rooms($load = true) {
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
		$conditions = array("status='global'");
		$range = $db->loadRange('chatserver_room', '*', $conditions, 'UID ASC');
		if (false === $range) { return false; }

		foreach($range as $item) { $this->members[$item['UID']] = $item; }
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

	//==============================================================================================
	//	XML IO with client
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	export rooms list as XML fragment
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent by [string]

	function toXml($indent = '') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return "<er>rooms list could not be loaded.</er>\n"; }

		$xml = $indent . "<rl>\n";
		foreach($this->members as $item) {
			$xml .= ''
			 . $indent . "<room>\n"
			 . $indent . "\t<uid>" . $item['UID'] . "</uid>\n"
			 . $indent . "\t<rh>" . $this->rh($item['UID']) . "</rh>\n"
			 . $indent . "\t<rm>" . $this->rm($item['UID']) . "</rm>\n"
			 . $indent . "</room>\n";
		}
		$xml .= $indent . "</rl>\n";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	process client announcement of new chat rooms
	//----------------------------------------------------------------------------------------------
	//arg: rnXml - XML announcement of new chat rooms to be made global [string]
	//returns: XML fragment [string]

	function addNewXml($rnXml) {
		$result = "\t<!-- client announces new chat rooms -->\n";		//%	return value [string]
		$xd = new KXmlDocument($rnXml);

		$children = $xd->getChildren();				//%	handles to children of root node [array]
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('room' == $child['type']) {
				$objAry = $xd->getChildren2d($childId);
				$result .= "\t<!-- new room " . $objAry['uid'] . " -->\n";

				//TODO: more error checking here

				$model = new Chatserver_Room();
				$model->UID = $objAry['uid'];
				$model->title = base64_decode($objAry['title64']);
				$model->description = base64_decode($objAry['description64']);
				$model->memberCount = '0';
				$model->status = 'global';
				$model->revision = 0;
				$model->createdOn = $objAry['createdon'];
				$model->createdBy = $objAry['createdby'];
				$model->editedOn = $objAry['editedon'];
				$model->editedBy = $objAry['editedby'];

				$report = $model->save();
				if ('' == $report) { 
					$result .= "\t<!-- saved " . $model->UID . " -->\n";
					$result .= "\t<rg>" . $model->UID . "</rg>\n";
				} else {
					$result .= "\t<!-- could not save " . $model->UID . " -->\n";
				}
			}
		}

		return $result;
	}

	//==============================================================================================
	//	HASHING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	ra - aggregate hash of all chat rooms and memberships
	//----------------------------------------------------------------------------------------------
	//returns: aggregate hash of all rooms and memberships, empty string on failure [bool]

	function ra() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$txt = '';
		foreach($this->members as $item) {
			$roomHash = $this->rh($item['UID']);			// hash the room
			$membershipHash = $this->rm($item['UID']);		// hash the memberships
			$txt .= $roomHash . '|' . $membershipHash . '|';
		}
		$hash = sha1($txt);
		//$this->set('ra', $hash);		//TODO: caching
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rl - room-list, list of hashes of all room states
	//----------------------------------------------------------------------------------------------
	//returns: serialized set of room UIDs and hashes, empty string on failure [string]

	function rl() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }	
		$rla = array();

		foreach($this->members as $item) {
			$roomHash = $this->rh($item['UID']);			// hash of room state
			$membershipHash = $this->rm($item['UID']);		// state of room membership
			$rla[] =  $item['UID'] . '|' . $roomHash . '|' . $membershipHash;
		}

		$rl = implode('||', $rla);
		return $rl;
	}

	//----------------------------------------------------------------------------------------------
	//.	rm - room membership hash
	//----------------------------------------------------------------------------------------------
	//arg: roomUID - UID of a Chatserver_Room object [string]

	function rm($roomUID) {
		global $db;
		$txt = '';
		$conditions = array("room='" . $db->addMarkup($roomUID) . "'");
		$range = $db->loadRange('chatserver_membership', '*', $conditions, 'user');
		foreach($range as $item) { $txt .= $item['user'] . "|" . $item['role'] . '|'; }
		$hash = sha1($txt);
		//$this->set('rm-' . $roomUID, $hash);		//TODO: cache this
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

		$model = new Chatserver_Room();
		$model->loadArray($this->members[$roomUID]);
		$hash = $model->rh();
		//$this->set('rh-' . $roomUID, $hash);		//TODO: cache this
		return $hash;
	}

	//==============================================================================================
	//	handle assertion of state of all rooms
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	assert state of all rooms (rl)
	//----------------------------------------------------------------------------------------------

	function assertRl($rl) {
		$rlAry = $this->rlToArray($rl);

		//------------------------------------------------------------------------------------------
		//	check that we agree on the state of all rooms asserted by the server
		//------------------------------------------------------------------------------------------

		foreach($rlAry as $room) {
			if ($room['rh'] !== $this->rh($room['UID'])) {
				// TODO: room hash mismatch, get complete state from central server
			}
			if ($room['rm'] !== $this->rm($room['UID'])) {
				// TODO: room membership hash mismatch, get memberships from central server
			}
		}

		//------------------------------------------------------------------------------------------
		//	delete any global chat rooms in local table which server does not report
		//------------------------------------------------------------------------------------------

		foreach($this->members as $room) {
			if (('global' == $room['state']) && (false == array_key_exists($room['UID'], $rlAry))) {
				$model = new Chat_Room($room['UID']);
				$check = $model->delete();
				if (true == $check) {
					echo "### deleted room " . $room['name'] . " (" . $room['UID'] . ")\n";
				} else {
					echo "### could not remove room " . $room['name'] . " (" . $room['UID'] . ")\n";
				}
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	convert room list (rl) hash set into an array of room hashes
	//----------------------------------------------------------------------------------------------

	function rlToArray($rl) {
		$rv = array();					//%	return value [array]
		$rooms = array();				//% set of raw hashes
		
		foreach($rooms as $hashset) {
			$hashes = explode('|', $hashset);
			$rv[$parts[0]] = array(
				'UID' => $parts[0],		/* UID of room          */
				'rh' => $parts[1],		/* room hash            */
				'rm' => $parts[2]		/* room membership hash */
			);
		}

		return $rv;
	}

}

?>
