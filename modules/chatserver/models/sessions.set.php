<?

	require_once($kapenta->installPath . 'modules/chatserver/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	collection object to represent global user sessions
//--------------------------------------------------------------------------------------------------

class Chatserver_Sessions {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;				//_	serialized Chatserver_Session objects [string]
	var $loaded = false;		//_	set to true when members loaded [bool]

	var $peerUID = '';			//_	UID of a Chatserver_Peer obkect [string]
	var $refreshAge = 120;		//_	Sessions marked live at this interval TODO: registry [int]


	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of a Chatserver_Peer object [string]
	//opt: load - set to false to use lazy initialization [bool]

	function Chatserver_Sessions($peerUID, $load = true) {
		$this->peerUID = $peerUID;
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load all current, global user sessions on the given peer
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failue [bool]

	function load() {
		global $db;
		$this->members = array();
		if ('' == $this->peerUID) { return false; }

		$conditions = array("serverUID='" . $db->addMarkup($this->peerUID) . "'");
		$range = $db->loadRange('chatserver_session', '*', $conditions, 'userUID ASC');
		if (false === $range) { return false; }

		foreach($range as $item) { $this->members[$item['userUID']] = $item; }
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a session for the given userUID is known on this peer
	//----------------------------------------------------------------------------------------------
	//;	note that this may be extened in future to allow multiple concurrent sessions per client
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if found, false if not [bool]

	function has($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		foreach($this->members as $item) {
			if ($item['userUID'] == $userUID) { return true; }
		}
		
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a record of a current / ongoing user session at this client
	//----------------------------------------------------------------------------------------------
	//;	note that this may be extended in future to allow mutiple concurrent sessions per client
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function add($userUID) {
		global $kapenta;
		global $db;

		if (false == $db->objectExists('users_user', $userUID)) { return false; }
		//TODO: verify $this->peerUID against chatserver_peer table
		$this->remove($userUID);

		//------------------------------------------------------------------------------------------
		//	create new global session
		//------------------------------------------------------------------------------------------

		$model = new Chatserver_Session();
		$model->status = 'global';
		$model->serverUID = $this->peerUID;
		$model->userUID = $userUID;
		$report = $model->save();

		if ('' == $report) {
			$args = array('userUID' => $userUID, 'peerUID' => $this->peerUID);
			$kapenta->raiseEvent('chatserver', 'chatserver_sessionstart', $args);
			return true;
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove any sessions for the given user on this peer
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true if session was removed, false if none found [bool]

	function remove($userUID) {
		global $kapenta;
		global $db;
	
		$found = false;
		$conditions = array();
		$conditions[] = "serverUID='" . $db->addMarkup($this->peerUID) . "'";
		$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";

		$range = $db->loadRange('chatserver_session', '*', $conditions);
		foreach($range as $item) {
			$model = new Chatserver_Session($item['UID']);
			$check = $model->delete();
			if (true == $check) {
				$found = true;
				$args = array('userUID' => $userUID);
				$kapenta->raiseEvent('chatserver', 'chatserver_sessionend', $args);
			} else {
				// TODO: handle failures here
			}
		}
		return $found;
	}

	//==============================================================================================
	//	CLIENT XML I/O
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	resets a client's local sessions list from XML document it sent
	//----------------------------------------------------------------------------------------------
	//arg: slXml - XML document listing all sessions on this client [string]
	//returns: XML fragment to be returned to the client [string]

	function setSessions($slXml) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return '<!-- could not load peer set -->'; }
		if ('' == $this->peerUID) { return ''; }

		$result = "\t<!-- resetting session list for peer " . $this->peerUID . " -->\n";
		$users = $this->slXmlToArray($slXml);
		
		//------------------------------------------------------------------------------------------
		//	add any new / unknown sessions
		//------------------------------------------------------------------------------------------
		//TODO: better lookup, avoid this n^2 loop / comparison

		foreach($users as $userUID) {
			if (false == $this->has($userUID)) {
				$check = $this->add($userUID);
				if (true == $check) { $result .= "<!-- added session for $userUID -->\n"; }
				else { $result .= "<!-- could not add session for  $userUID -->\n"; }
			}
			$result .= "\t<sg>" . $userUID . "</sg>\n";
		}

		//------------------------------------------------------------------------------------------
		//	remove any sessions known to us but no longer reported by the client (expired/logout)
		//------------------------------------------------------------------------------------------
		foreach($this->members as $item) {
			if (false == in_array($item['userUID'], $users)) {
				$check = $this->remove($item['userUID']);
				if (true == $check) {
					$result .= "<!-- removed inactive session for " . $item['userUID'] . " -->\n";
				} else {
					$result .= "<!-- could not remove session for " . $item['userUID'] . " -->\n";
				}
			}
		}

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert XML sessions list to array
	//----------------------------------------------------------------------------------------------
	//arg: slXml - XML document listing all sessions on this client [string]
	//returns: set of Users_User UIDs [array]
	//; note that this may be extened in future to allow multiple sessions on each client
	
	function slXmlToArray($slXml) {
		$users = array();

		$xd = new KXmlDocument($slXml);
		$root = $xd->getEntity(1);				// get the XML root entity [dict]		
		//echo "<!-- *** root type: " . $root['type'] . " -->\n";

		if ('localsessions' == $root['type']) {
			$children = $xd->getChildren();		// handles to all children of root entity [array]
			foreach($children as $childId) {
				$child = $xd->getEntity($childId);
				if ('u' == $child['type']) {
					$users[] = $child['value'];
					//echo "<!-- *** parser found user session " . $child['value'] . " -->\n";
				}
			}
		}

		return $users;
	}

	//----------------------------------------------------------------------------------------------
	//.	process a client's notification of new sessions
	//----------------------------------------------------------------------------------------------
	//arg: snXml - XML fragment listing new user sessions [string]
	//returns: XML fragment [string]

	function addSessionsXml($snXml) {
		$result = "\t<!-- processing new sessions -->\n";			//%	return value [string]
		$xd = new KXmlDocument($snXml);
		$root = $xd->getEntity(1);

		if ('sn' == $root['type']) {
			$children = $xd->getChildren();
			foreach($children as $childId) {
				$child = $xd->getEntity($childId);
				if ('u' == $child['type']) {
					$check = $this->add($child['value']);
					if (true == $check) {
						$result .= "\t<!-- added new session " . $child['value'] . " -->\n";
						$result .= "\t<sg>" . $child['value'] . "</sg>\n";
					}
				}
			}
		}

		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this set of sessions to XML
	//----------------------------------------------------------------------------------------------

	function toXml() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		$xml = "<sessions>\n";
		foreach($this->members as $item) {
			if ('global' == $item['status']) {
				$xml .= "\t<u>" . $item['userUID'] . "</u>\n";
			}
		}
		$xml .= "</sessions>";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	mark sessions in the database as live/current
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function markAllLive() {
		global $kapenta;
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }

		$allOk = true;										//%	return value [bool]
		$now = $kapenta->time();							//%	current timestamp [int]

		foreach($this->members as $item) {
			//--------------------------------------------------------------------------------------
			//	if updated more than two minutes ago, update it //TODO: make TTL registry setting
			//--------------------------------------------------------------------------------------
			$lastUpdated = $kapenta->strtotime($item['editedOn']);
			if (($now - $lastUpdated) > $this->refreshAge) {
				$model = new Chatserver_Session($item['UID']);
				$model->editedOn = $kapenta->datetime();
				$report = $model->save();
				if ('' != $report) { $allOk = false; }
			}

		}
	
		return $allOk;
	}

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	hash of all sessions local to this server
	//----------------------------------------------------------------------------------------------
	//returns: hash of all peer's sessions or empty string on failure [string]

	function sl() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$txt = '';
		foreach($this->members as $item) { $txt .= $item['userUID'] . '|' . $item['status'] . '|'; }

		$hash = sha1($txt);
		//$this->set('sp-' . $peerUID, $hash);		//TODO: cache this
		return $hash;
	}

}

?>
