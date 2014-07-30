<?

//--------------------------------------------------------------------------------------------------
//*	set of peer servers which chat through this server module
//--------------------------------------------------------------------------------------------------

class Chatserver_Peers {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $members;			//_	serialized CHatserver_Peer objects [array]
	var $loaded = false;	//_	set to true when memebrs loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: load - set to false to enable lazy initialization [string]

	function Chatserver_Peers($load = true) {
		$this->members = array();
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load all peers from the database
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;
		$this->members = array();

		$conditions = array();
		$range = $db->loadRange('chatserver_peer', '*', $conditions, 'peerUID');
		if (false === $range) { return false; }

		foreach($range as $item) { $this->members[$item['peerUID']] = $item; }

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	express this set of peers as an XML fragment
	//----------------------------------------------------------------------------------------------

	function toXml($indent) {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return '<!-- could not load peers -->'; }
		$xml .= $indent . "<pl>\n";		// pl - peer list

		foreach($this->members as $item) {
			$set = new Chatserver_Sessions($item['UID'], true);
			$sl = $set->sl();

			$xml .= ''
			 . $indent . "<peer>\n"
			 . $indent . "\t<uid>" . $item['peerUID'] . "</uid>\n"
			 . $indent . "\t<name>" . $item['name'] . "</name>\n"
			 . $indent . "\t<url>" . $item['url'] . "</url>\n"
			 . $indent . "\t<sl>" . $sl . "</sl>\n"
			 . $indent . "</peer>\n";

		}

		$xml .= $indent . "</pl>\n";
		return $xml;
	}

	//==============================================================================================
	//	HASHING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	nk - hash of all peers in network
	//----------------------------------------------------------------------------------------------

	function nk() {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return ''; }

		$txt = '';					//%	plaintext from which hash is derived [string]

		foreach($this->members as $item) {
			$set = new Chatserver_Sessions($item['UID'], true);
			$sl = $set->sl();
			$txt = $item['peerUID'] . "|" . $item['name'] . '|' . $item['url'] . '|' . $sl . "\n";
		}

		$hash = sha1($txt);
		//$this->set('nk', $hash);		//TODO: cache this
		return $hash;
	}

}

?>
