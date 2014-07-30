<?

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of peers in the chat network
//--------------------------------------------------------------------------------------------------

class Chat_Peers {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;							//_	serialized Chat_Peer objects [array]
	var $loaded = false; 			//_	set to true when peers are loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: load - set to false to use lazy initialization [bool]

	function Chat_Peers($load = true) {
		$this->members = array();
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load all chat_peer objects into members array
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;
		$this->members = array();
		$conditions = array();
		$range = $db->loadRange('chat_peer', '*', $conditions, 'peerUID');

		if (false === $range) { return false; }
		foreach($range as $item) { $this->members[$item['UID']] = $item; }
		$this->loaded = true;
		return true;
	}

	//==============================================================================================
	//	CHATSERVER I/O
	//==============================================================================================
	
	//----------------------------------------------------------------------------------------------
	//.	process list of peers sent by the server
	//----------------------------------------------------------------------------------------------
	//returns: reporting / error log [string]

	function checkPeerListXml($plXml) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return 'could not load peers from database<br/>\n'; }

		$log = '';
		$videoUIDs = array();
		$xd = new KXmlDocument($plXml);
		$root = $xd->getEntity(1);
		if ('pl' != $root['type']) { return 'Invalid peer list XML<br/>\n'; }

		//------------------------------------------------------------------------------------------
		//	create, check or update all peers reported by server
		//------------------------------------------------------------------------------------------
		
		$children = $xd->getChildren();
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('peer' == $child['type']) {
				//----------------------------------------------------------------------------------
				//	get details of a peer
				//----------------------------------------------------------------------------------
				$parts = $xd->getChildren2d($childId);
				$allOk = true;

				if (false == array_key_exists('uid', $parts)) { $allOk = false; }
				if (false == array_key_exists('name', $parts)) { $allOk = false; }
				if (false == array_key_exists('url', $parts)) { $allOk = false; }
				if (false == array_key_exists('sl', $parts)) { $allOk = false; }

				if (true == $allOk) {
					//------------------------------------------------------------------------------
					//	check against database
					//------------------------------------------------------------------------------
					$this->createOrUpdate($parts['uid'], $parts['name'], $parts['url']);
					$videoUIDs[] = $parts['uid'];

					//------------------------------------------------------------------------------
					//	check hash
					//------------------------------------------------------------------------------
					if ($parts['sl'] != $this->sl($parts['uid'])) {
						$log .= "Session hashes mismatch, re-downloading for this peer.<br/>\n";	
						$set = new Chat_Sessions($parts['uid']);
						$set->resetFromServer();
					}

					
					

				} else { $log .= "Malformed XML, missing required field.<br/>"; }

			}
		}

		//------------------------------------------------------------------------------------------
		//	remove any peers not reported by server
		//------------------------------------------------------------------------------------------

		foreach($this->members as $item) {
			if (false == in_array($item['UID'], $videoUIDs)) {
				$model = new Chat_Peer($item['UID']);
				$model->delete();
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	create or update chat_peer object
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function createOrUpdate($UID, $name, $url) {
		$different = false;
		$model = new Chat_Peer($UID, true);

		if (false == $model->loaded) {
			$different = true;											//	unknown peer
		} else {
			if ($model->peerUID != $UID) { $different = true; }			//	known peer
			if ($model->peerUrl != $url) { $different = true; }
			if ($model->peerName != $name) { $different = true; }
		}

		if (true == $different) {
			$model->peerUID = $UID;
			$model->peerUrl = $url;
			$model->peerName = $name;
			$report = $model->save();
			if ('' != $report) { return false; }
		}

		return true;
	}

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	nk - network hash, all peers chatting through this server
	//----------------------------------------------------------------------------------------------
	//returns: hash of all peers, or empty string on failure [string]

	function nk() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$txt = '';					//%	plaintext from which hash is derived [string]

		foreach($this->members as $item) {
			$sl = $this->sl($item['UID']);
			$txt = $item['peerUID'] ."|". $item['peerName'] .'|'. $item['peerUrl'] .'|'. $sl . "\n";
		}

		$hash = sha1($txt);
		//$this->set('nk', $hash);
		return $hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	sl - get the hash of all sessions local to a peer
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of a Chat_Peer object [string]	

	function sl($peerUID) {
		$set = new Chat_Sessions($peerUID, true);
		$sl = $set->sl();
		return $sl;
	}

}

?>
