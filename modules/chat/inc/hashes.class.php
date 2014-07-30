<?

	require_once($kapenta->installPath . 'modules/chat/models/hash.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	utility object for calculating hashes of various datasets
//--------------------------------------------------------------------------------------------------

class Chat_HashesDeprecated {

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chat_Hashes() {
		// placeholder - nothing to do as yet
	}

	//----------------------------------------------------------------------------------------------
	//.	get a stored hash by label
	//----------------------------------------------------------------------------------------------
	//returns: hash if found, empty string if not found [string]

	function get($label) {
		$model = new Chat_Hash($label);
		if (false == $model->loaded) { return ''; }
		return $model->hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a hash
	//----------------------------------------------------------------------------------------------

	function set($label, $hash) {
		$model = new Chat_Hash($label, true);
		$model->label = $label;
		$model->hash = $hash;
		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	nk - network hash, all peers chatting through this server
	//----------------------------------------------------------------------------------------------

	function nk() {
		global $db;
		$txt = '';					//%	plaintext from which hash is derived [string]

		$range = $db->loadRange('chat_peer', '*', '', 'peerUID');

		foreach($range as $item) {
			$txt = $item['peerUID'] . "|" . $item['peerName'] . '|' . $item['peerUrl'] . "\n";
		}

		$hash = sha1($txt);
		$this->set('nk', $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	ra - aggregate hash of all chat rooms and memberships
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: use Chat_Rooms object to calculate

	function ra() {
		global $db;
		$txt = '';
		$conditions = array("state='global'");
		$range = $db->loadRange('chat_room', '*', $conditions, 'UID');
		foreach($range as $item) {
			$roomHash = $this->rh($item['UID']);			// hash the room
			$membershipHash = $this->rm($item['UID']);		// hash the memberships
			$txt .= $roomHash . '|' . $membershipHash . '|';
		}
		$hash = sha1($txt);
		$this->set('ra', $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rh - room hash
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: use Chat_Rooms object to calculate
	//arg: roomUID - UID of a Chat_Room object [string]

	function rh($roomUID) {
		$txt = '';
		$model = new Chat_Room($roomUID);
		if (true == $model->loaded) {
			$txt = $model->UID . '|' . $model->title . '|' . $model->description;
		}
		$hash = sha1($txt);
		$this->set('rh-' . $roomUID, $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rm - room membership hash
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: use Chat_Memberships object to calculate
	//arg: roomUID - UID of a Chat_Room object [string]

	function rm($roomUID) {
		global $db;
		$txt = '';
		$conditions = array("room='" . $db->addMarkup($roomUID) . "'");
		$range = $db->loadRange('chat_membership', '*', $conditions, 'user');
		foreach($range as $item) { $txt .= $item['user'] . "|" . $item['role'] . '|'; }
		$hash = sha1($txt);
		$this->set('rm-' . $roomUID, $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert room list (rl) hash set into an array of room hashes
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: use Chat_Rooms object to calculate

	function rlToArray($rl) {
		$rv = array();					//%	return value [array]
		$rooms = array();				//% set of raw hashes
		
		foreach($rooms as $hashset) {
			$hashes = explode('|', $hashset);
			$rv[] = array(
				'UID' => $parts[0],		/* UID of room          */
				'rh' => $parts[1],		/* room hash            */
				'rm' => $parts[2]		/* room membership hash */
			);
		}

		return $rv;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	sh - session hash, all local sessions (ie, users_session, not chat_session)
	//----------------------------------------------------------------------------------------------

	function sl() {
		global $db;

		$txt = '';

		$conditions = array("status='active'");
		$range = $db->loadRange('users_session', 'UID, createdBy', $conditions, 'createdBy');
		foreach($range as $item) { $txt .= $item['createdBy'] . '|'; }
		$hash = sha1($txt);
	
		$this->set('sl', $hash);
		return $hash;		
	}

}

?>
