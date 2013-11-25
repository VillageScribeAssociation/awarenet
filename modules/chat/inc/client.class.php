<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/messageout.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/hash.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/hashes.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/peers.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/rooms.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/session.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/sessions.set.php');
	require_once($kapenta->installPath . 'modules/chat/models/inboxes.set.php');

//--------------------------------------------------------------------------------------------------
//*	chat server client
//--------------------------------------------------------------------------------------------------

class Chat_Client {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $server = '';				//_	URL of chat server [string]
	var $myUID = '';				//_	UID of this peer [string]
	var $enabled = false;			//_	chat enabled [bool]

	var $debug = false;				//_	print noisy disgnostics [bool]
	var $log = '';					//_	debug log [string]

	var $hashes;					//_	[object:Chat_Hashes]
	var $peers;						//_	[object:Chat_Peers]
	var $rooms;						//_	[object:Chat_Rooms]
	var $sessions;					//_	[object:CHat_Sessions]

	var $request = '';				//_	request to be sent to the server [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chat_Client($prefix = '', $postfix = '') {
		global $kapenta;
		$this->server = $kapenta->registry->get('chat.server');
		$this->myUID = $kapenta->registry->get('p2p.server.uid');

		$this->hashes = new Chat_Hashes();
		$this->peers = new Chat_Peers();
		$this->rooms = new Chat_Rooms();
		$this->sessions = new Chat_Sessions($this->myUID);

		if ('yes' == $kapenta->registry->get('chat.enabled')) { $this->enabled = true; }
	}

	//----------------------------------------------------------------------------------------------
	//.	push any outgoing messages as check the server's state
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function check() {
		$io = new Chat_IO();

		$this->setOwnState();

		$msg = "<cc>\n" . $this->request . "</cc>\n";

		$this->log .= "<pre>\n";
		$this->log .= "<h2>Request...</h2>" . htmlentities($msg);

		$response = $io->send('check', '', $msg);

		$this->log .= "<h2>Response...</h2>";
		$this->log .= "<textarea rows='10' style='width: 100%'>$response</textarea><br/>\n";

		$this->log .= "<h2>Processing...</h2>";
		$this->process(trim($response));
		$this->log .= "</pre>\n";
	}

	//----------------------------------------------------------------------------------------------
	//.	process response from the chat server
	//----------------------------------------------------------------------------------------------

	function process($response) {
		global $db;

		$xd = new KXmlDocument($response);

		$children = $xd->getChildren();

		foreach($children as $childId) {
			$entity = $xd->getEntity($childId);
			$type = strtolower($entity['type']);
			$data = $entity['value'];

			switch($type) {

				//----------------------------------------------------------------------------------
				//	server reports an error
				//----------------------------------------------------------------------------------

				case 'er':
					$this->log .= "*** server reports error: $data<br/>\n";
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server asserts new messages
				//----------------------------------------------------------------------------------

				case 'mu':
					$set = new Chat_Inboxes();
					$check = $set->storeMessages('<mu>' . $data . '</mu>');
					if (true == $check) { $this->log .= "*** messages added to local inboxes<br/>\n"; }
					else { $this->log .= "*** messages NOT added to local inboxes<br/>\n"; }
					break;		//..................................................................


				//----------------------------------------------------------------------------------
				//	server confirms our receipt of a message
				//----------------------------------------------------------------------------------

				case 'mc':
					$set = new Chat_Inboxes();
					$check = $set->confirmMessages('<mc>' . $data . '</mc>');
					if (true == $check) { $this->log .= "*** messages confirmed in local cache<br/>\n"; }
					else { $this->log .= "*** messages NOT confirmed in local cache<br/>\n"; }	
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server requests we clear chat_peer table
				//----------------------------------------------------------------------------------

				case 'nc':
					$this->log .= "*** emptying chat_peer table\n";
					$db->query('delete from chat_peer');
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server asks that we recalculate network hash
				//----------------------------------------------------------------------------------
				case 'nh':
					$nk = $this->peers->nk();
					$this->log .= "*** recalculating network hash: $nk\n";
					break;		//..................................................................		

				/*
				// deprecated, may still be used in response to an event on the server
				//----------------------------------------------------------------------------------
				//	server asserts network peer (TODO: lots of input testing here)
				//----------------------------------------------------------------------------------
				case 'np':
					$parts = explode('|', $data);
					$this->log .= "*** peer uid: " . $parts[0] . "\n";
					$this->log .= "*** peer name: " . $parts[1] . "\n";
					$this->log .= "*** peer url: " . $parts[2] . "\n";

					$model = new Chat_Peer();
					$model->peerUID = $parts[0];
					$model->peerUrl = $parts[2];
					$model->peerName = $parts[1];
					$model->shared = 'no';
					$report = $model->save();

					if ('' == $report) { $this->log .= "*** peer saved\n"; }
					else { $this->log .= "*** could not save peer\n"; }

					break;		//------------------------------------------------------------------
				*/

				//----------------------------------------------------------------------------------
				//	server asserts state of all peers
				//----------------------------------------------------------------------------------

				case 'pl':
					$this->peers->checkPeerListXml('<pl>' . $data . '</pl>');
					break;		//------------------------------------------------------------------

				//----------------------------------------------------------------------------------
				//	server asserts that one of our local rooms is now a global object
				//----------------------------------------------------------------------------------

				case 'rg':
					$model = new Chat_Room($data);
					if (true == $model->loaded) {
						$model->state = 'global';
						$report = $model->save();
						if ('' == $report) { $this->log .= "### room now global: " . $model->title . "\n"; }
						else { $this->log .= "## could not make room global: " . $model->title . "\n"; }
						$ra = $this->rooms->ra();
						$this->log .= "### new rooms-all hash: $ra\n";
					} else {
						$this->log .= "### local room not found: $data\n";
					}
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server asserts state of all chat rooms
				//----------------------------------------------------------------------------------

				case 'rl':
					$this->rooms->checkListXml('<rl>' . $data . '</rl>');					
					//TODO: ... 

					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server asserts that one of our local sessions is now a global object
				//----------------------------------------------------------------------------------
				
				case 'sg':
					$this->log .= "*** marking session global: " . $data . "<br/>\n";
					$check = $this->sessions->markGlobal($data);
					break;		//..................................................................

				//----------------------------------------------------------------------------------
				//	server requests we resend all local sessions
				//----------------------------------------------------------------------------------
				
				case 'sr':
					$this->sessions->updateServer();
					break;		//..................................................................

				default:
					$this->log .= "*** unrecognized entity: " . $entity['type'] . "\n";
					break;		
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	make outgoing message to describe our own state to the server
	//----------------------------------------------------------------------------------------------
	//TODO: re-enable caching of hashes

	function setOwnState() {
		//------------------------------------------------------------------------------------------
		// calculate hashes (forces db load)
		//------------------------------------------------------------------------------------------
		$nk = $this->peers->nk();
		$sl = $this->sessions->sl();
		$ra = $this->rooms->ra();

		//------------------------------------------------------------------------------------------
		//	add new sessions created locally 
		//------------------------------------------------------------------------------------------

		if (true == $this->sessions->hasLocal) {
			$this->request .= "\t<!-- announce new sessions -->\n";
			$this->request .= $this->sessions->localSessionsXml("\t");
		}

		//------------------------------------------------------------------------------------------
		// add new chat rooms created locally
		//------------------------------------------------------------------------------------------
		if (true == $this->rooms->hasLocal) {
			$this->request .= "\t<!-- announce local rooms -->\n";			
			$this->request .= $this->rooms->localRoomsXml("\t");
		}

		//------------------------------------------------------------------------------------------
		// add any outgoing messages
		//------------------------------------------------------------------------------------------
		//TODO: this

		//------------------------------------------------------------------------------------------
		// add confirmation of messages received by this client, but not yet delivered
		//------------------------------------------------------------------------------------------
		$inboxes = new Chat_Inboxes();
		$this->request .= $inboxes->getHasXml("\t");

		//------------------------------------------------------------------------------------------
		// add overall hashes
		//------------------------------------------------------------------------------------------
		//if ('' == $nk) { $nk = $this->hashes->nk(); }
		//if ('' == $sl) { $sl = $this->hashes->sl(); }
		//if ('' == $ra) { $ra = $this->hashes->ra(); }

		$this->request .= ''
		 . "\t<nk>$nk</nk>\n"				/* assert network hash */
		 . "\t<sl>$sl</sl>\n"				/* assert local sessions hash */
		 . "\t<ra>$ra</ra>\n"				/* assert global rooms hash */
		 . '';


	}

}

?>
