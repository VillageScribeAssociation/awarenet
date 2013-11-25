<?

	require_once($kapenta->installPath . 'modules/chatserver/models/hash.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	utility object for calculating hashes of various datasets
//--------------------------------------------------------------------------------------------------

class Chatserver_Hashes {

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chatserver_Hashes() {
		// placeholder - nothing to do as yet
	}

	//----------------------------------------------------------------------------------------------
	//.	get a stored hash by label
	//----------------------------------------------------------------------------------------------
	//returns: hash if found, empty string if not found [string]

	function get($label) {
		$model = new Chatserver_Hash($label, true);
		if (false == $model->loaded) { return ''; }
		return $model->hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a hash
	//----------------------------------------------------------------------------------------------

	function set($label, $hash) {
		$model = new Chatserver_Hash($label, true);
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

		$range = $db->loadRange('chatserver_peer', '*', '', 'UID');

		foreach($range as $item) {
			$txt = $item['peerUID'] . "|" . $item['name'] . '|' . $item['url'] . "\n";
		}

		$hash = sha1($txt);
		$this->set('nk', $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	ra - aggregate hash of all chat rooms and memberships
	//----------------------------------------------------------------------------------------------

	function ra() {
		global $db;
		$txt = '';
		//$conditions = array("state='global'"); // all rooms are global on the server
		$range = $db->loadRange('chatserver_room', '*', '', 'UID');
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
	//arg: roomUID - UID of a Chatserver_Room object [string]

	function rh($roomUID) {
		$txt = '';
		$model = new Chatserver_Room($roomUID);
		if (true == $model->loaded) {
			$txt = $model->UID . '|' . $model->title . '|' . $model->description;
		}
		$hash = sha1($txt);
		$this->set('rh-' . $roomUID, $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	rl - room-list, list of hashes of all room states
	//----------------------------------------------------------------------------------------------

	function rl() {
		global $db;
		$range = $db->loadRange('chatserver_room', '*', '', 'UID');
		$rla = array();
		foreach($range as $item) {
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
		$this->set('rm-' . $roomUID, $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	sp - sessions on peer
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of awareNet instance whose sessions we're interested in [string]

	function sp($peerUID) {
		global $db;
		$conditions = array("serverUID='" . $db->addMarkup($peerUID) . "'");
		$range = $db->loadRange('chatserver_session', 'UID, userUID', $conditions, 'userUID');
		$txt = '';
		foreach($range as $item) {
			$txt .= $item['userUID'] . '|';
		}
		$hash = sha1($txt);
		$this->set('sp-' . $peerUID, $hash);
		return $hash;
	}

}

?>
