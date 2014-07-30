<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent the set of user sessions at a peer
//--------------------------------------------------------------------------------------------------

class Chat_Sessions {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;					//_	serialized chat_session objects [array]
	var $loaded = false;			//_	set to true when members loaded [bool]

	var $peerUID = '';				//_	UID of a Chat_Peer object [string]
	var $hasLocal = false;			//_	set to true if (new) local sessions are found [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: peerUID - UID of a Chat_Peer object [string]
	//opt: load - set to false to use lazy initialization [bool]

	function Chat_Sessions($peerUID = '', $load = true) {
		$this->members = array();
		$this->peerUID = $peerUID;
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load the set of currently active sessions known to this peer
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;
		if ('' == $this->peerUID) { return false; }

		$conditions = array("serverUID='" . $db->addMarkup($this->peerUID) . "'");
		$range = $db->loadRange('chat_session', '*', $conditions, 'userUID ASC');
		if (false === $range) { return false; }
		
		foreach ($range as $item) {
			$this->members[] = $item;
			if ('local' == $item['status']) { $this->hasLocal = true; }
		}
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	mark a chat session as global (confirmed by central server)
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function markGlobal($userUID) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }		

		$marked = false;										//%	return value [bool]

		foreach($this->members as $item) {
			if ($item['userUID'] == $userUID) {
				$model = new Chat_Session($item['UID']);
				$model->status = 'global';
				$report = $model->save();
				if ('' == $report) { $marked = true; }
			}
		}

		return $marked;
	}

	//==============================================================================================
	//	CHAT SERVER I/O
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	send list of current sessions to server
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function updateServer() {
		echo "*** updating server with complete list of local sessions.<br/>\n";

		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		$io = new Chat_IO();		
		$msg = $this->getLocalXml();
		$response = $io->send('setsessions', '', $msg);

		echo "*** server response:<br/><textarea rows='10' style='width: 100%'>$response</textarea><br/>\n";
		//TODO: error checking here

		$xd = new KXmlDocument($response);
		$root = $xd->getEntity(1);
		if ('sessions' == $root['type']) {
			$children = $xd->getChildren();
			foreach($children as $childId) {
				$child = $xd->getEntity($childId);
				if ('sg' == $child['type']) { $this->markGlobal($child['value']); }
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	create XML list of local sessions
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment, or empty string on failure [string]	

	function getLocalXml($indent = '') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$xml = $indent . "<localsessions>\n";
		foreach($this->members as $item) { $xml .= $indent . "\t<u>" . $item['userUID'] . "</u>"; }
		$xml .= $indent . "</localsessions>\n";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	create XML list of NEW local sessions (not yet confirmed by server)
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment, or empty string on failure [string]	

	function localSessionsXml($indent) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$xml = $indent . "<sn>\n";
		foreach($this->members as $item) {
			if ('local' == $item['status']) {
				$xml .= $indent . "\t<u>" . $item['userUID'] . "</u>\n";
			}
		}
		$xml .= $indent . "</sn>\n";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	download from central server all sessions active on this peer
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function resetFromServer() {	
		//------------------------------------------------------------------------------------------
		//	query central chat server
		//------------------------------------------------------------------------------------------
		$io = new Chat_IO();
		$response = $io->send('getsessions', $this->peerUID, '');
		if ('' == $response) { return true; }

		//------------------------------------------------------------------------------------------
		//	parse resposne
		//------------------------------------------------------------------------------------------
		$userUIDs = array();
		$xd = new KXmlDocument($response);
		$root = $xd->getEntity(1);
		if ('sessions' != $root['type']) { return false; }

		$children = $xd->getChildren();
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('u' == $child['type']) { $userUIDs[] = $child['value']; }
		}

		//------------------------------------------------------------------------------------------
		//	add any new sessions
		//------------------------------------------------------------------------------------------
		//------------------------------------------------------------------------------------------
		//	add any new sessions
		//------------------------------------------------------------------------------------------

	}

	//==============================================================================================
	//	HASHES
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	sl - local session hash, all sessions known on this server
	//----------------------------------------------------------------------------------------------
	//returns: hash of all user sessions on this peer, empty string on failure [string]

	function sl() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }
		$txt = '';

		foreach($this->members as $item) { $txt .= $item['userUID'] . '|' . $item['status'] . '|'; }
		$hash = sha1($txt);	
		//$this->set('sl', $hash);			//TODO: cache this
		return $hash;
	}

}

?>
