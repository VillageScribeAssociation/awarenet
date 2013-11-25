<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/peers.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/rooms.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/sessions.set.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/messages.set.php');

//--------------------------------------------------------------------------------------------------
//*	object which verified and responds to client chat requests
//--------------------------------------------------------------------------------------------------

class Chatserver_Server {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $loaded = false;			//_	set to true when object initialized correctly [bool]	

	var $peerUID = '';				//_	UID of a Chatserver_Peer object [string]
	var $peer;						//_	current peer [object: Chatserver_Peer]

	var $peers;						//_	set of peers in chat network [string]
	var $rooms;						//_	set of currently active chat rooms [string]
	var $sessions;					//_	set of active sessions on this peer [string]
	var $messages;

	var $response = '';				//_	XML document which will be returned to the client [string]

	var $nk = '';					//_	network hash asserted by client [string]
	var $ra = '';					//_	'rooms all' aggregate hash asserted by client [string]
	var $sl = '';					//_	hash of 'sessions local' to this client [string]
	var $sg = '';					//_	'sessions-global' hash, hash of all sessions [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of a Chatserver_Peer object [string]
	//TODO: add RSA signature checking here	

	function Chatserver_Server($peerUID) {
		//------------------------------------------------------------------------------------------
		//	load peer making this request
		//------------------------------------------------------------------------------------------
		$this->peerUID = $peerUID;
		$this->peer = new Chatserver_Peer($peerUID, true);
		$this->loaded = $this->peer->loaded;

		//------------------------------------------------------------------------------------------
		//	lazily initialized helper / collection objects
		//------------------------------------------------------------------------------------------
		$this->peers = new Chatserver_Peers(false);
		$this->rooms = new Chatserver_Rooms(false);
		$this->sessions = new Chatserver_Sessions($peerUID, false);
		$this->messages = new Chatserver_Messages();

		//TODO:
	}

	//----------------------------------------------------------------------------------------------
	//.	return repsposne to the client
	//----------------------------------------------------------------------------------------------

	function sendResponse() {
		echo "<cs>\n" . $this->response . "</cs>\n";
	}

	//----------------------------------------------------------------------------------------------
	//.	process request XML document
	//----------------------------------------------------------------------------------------------
	//arg: xml - XML request document sent by client [string]

	function process($xml) {
		$allowSr = true;					//%	allow request reset of local sessions [bool]

		$xd = new KXmlDocument($xml);
		$children = $xd->getChildren();		//% children of root node [array]
		
		foreach($children as $childId) {
			$entity = $xd->getEntity($childId);
			$data = $entity['value'];
			switch(strtolower($entity['type'])) {

				//==================================================================================
				//	client hashes stored for comparison
				//==================================================================================
				case 'nk': $this->nk = $data; break;		//	'network' hash
				case 'ra': $this->ra = $data; break;		//	'rooms-all' hash
				case 'sl': $this->sl = $data; break;		//	'sessions-local' hash

				//==================================================================================
				//	client messages to be processed immediately
				//==================================================================================

				//----------------------------------------------------------------------------------
				//	client asserts that is has message(s) in local inbox
				//----------------------------------------------------------------------------------

				case 'mh':
					$messages = new Chatserver_Messages();
					$this->response .= "<!-- recording mh -->\n";
					$this->response .= $messages->recordHas('<mh>' . $data . '</mh>', "\t");
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	client announces new local chat room
				//----------------------------------------------------------------------------------

				case 'rn':
					$this->response .= $this->rooms->addNewXml('<rn>' . $data . '</rn>');
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	client asserts new local sessions
				//----------------------------------------------------------------------------------

				case 'sn':
					$this->response .= $this->sessions->addSessionsXml('<sn>' . $data . '</sn>');
					$allowSr = false;
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	unrecognized XML element
				//----------------------------------------------------------------------------------

				default:
					$this->response .= "\t<er>unknown entity: ". $entity['type'] ."</er>\n";
					break;		//..................................................................
			}

		}

	}

	//----------------------------------------------------------------------------------------------
	//.	check that hashes sent by client match expected values
	//----------------------------------------------------------------------------------------------

	function checkHashes() {
		//------------------------------------------------------------------------------------------
		//	check network hash
		//------------------------------------------------------------------------------------------
		$nk = $this->peers->nk();
		if ($this->nk != $nk) {
			// we disagree, send current peer list to client
			$this->response .= "\t<!-- network hash mismatch: nk: $nk to " . $this->nk . " -->\n";
			$this->response .= $this->peers->toXml("\t");
		} else {
			//$this->response .= "\t<!-- network hashes match $nk == " . $this->nk . " -->\n";
		}

		//------------------------------------------------------------------------------------------
		//	check rooms hash
		//------------------------------------------------------------------------------------------
		$ra = $this->rooms->ra();
		if ($ra != $this->ra) {
			// we disagree, send client list of all room hashes
			$this->response .= "\t<!-- rooms-all hash mismach $ra != " . $this->ra . " -->\n";
			$this->response .= $this->rooms->toXml("\t");
		} else {
			//$this->response .= "\t<!-- rooms-all hash matches $ra == $data -->\n";
		}

		//------------------------------------------------------------------------------------------
		//	check local sessions hash
		//------------------------------------------------------------------------------------------
		$sl = $this->peer->sl();
		if ($sl != $this->sl) {
			$this->response .= "\t<!-- session local hash mismatch: $sl != " . $this->sl . " -->\n";
			//if (true == $allowSr) {
				$this->response .= ''
					 . "\t<!-- request reset sessions from " . $this->peerUID . " -->\n"
					 . "\t<sr>please</sr>\n";
			//} else {
			//	$this->response .= "\t<!-- not requesting reset until sg handled -->\n";
			//}

		} else { 
			//--------------------------------------------------------------------------------------
			//	session hashes match expected value, mark sessions from this client as live
			//--------------------------------------------------------------------------------------
			$check = $this->peer->sessions->markAllLive();
			if (true == $check) { $this->response .= "\t<!-- marked all sessions live -->\n"; }
			//$this->response .= "\t<!-- session hashes match $sp == $data -->\n";			
		}


	}

	//----------------------------------------------------------------------------------------------
	//	add outgoing chat messages *after* everything else has been processed
	//----------------------------------------------------------------------------------------------

	function checkMessages() {
		$this->response .= $this->messages->outgoingToXml($this->peerUID, "\t");
	}

}

?>
