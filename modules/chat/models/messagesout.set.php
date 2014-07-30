<?

	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');
	require_once($kapenta->installPath . 'modules/chat/models/messageout.mod.php');

//--------------------------------------------------------------------------------------------------
//*	collection object to manage outgoing messages to be sent to the server
//--------------------------------------------------------------------------------------------------

class Chat_MessagesOut {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $members;						//_	serialized Chat_MessageOut object [array]
	var $loaded = false;				//_	set to true when messages loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: load - set to false to use lazy initialization [bool]

	function Chat_MessagesOut($load = true) {
		if (true == $load) { $this->load(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load any outgoing messages from database
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function load() {
		global $db;
		$range = $db->loadRange('chat_messageout', '*', array());
		if (false === $range) { return false; }
		$this->members = $range;
		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert to XML to be sent to server
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment [string]

	function toXml($indent = '') {
		if (false == $this->loaded) { $this->load(); }
		if (false == $this->loaded) { return false; }
		if (0 == count($this->members)) { return false; }

		$xml = $indent . "<mn>\n";

		foreach($this->members as $item) {
			$xml .= ''
			 . $indent . "\t<message>\n"
			 . $indent . "\t\t<uid>" . $item['UID'] . "</uid>\n"
			 . $indent . "\t\t<room>" . $item['toRoom'] . "</room>\n"
			 . $indent . "\t\t<fromuser>" . $item['fromUser'] . "</fromuser>\n"
			 . $indent . "\t\t<touser>" . $item['toUser'] . "</touser>\n"
			 . $indent . "\t\t<message64>" . base64_encode($item['message']) . "</message64>\n"
			 . $indent . "\t</message>\n";
		}

		$xml .= $indent . "</mn>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	send/move messages to server (synchronous)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function send() {
		global $kapenta;

		$allOk = true;
		$xml = $this->toXml();
		if ('' == $xml) { return false; }

		$kapenta->logEvent('chatclient', 'messagesout.set', 'send()::out', $xml);
		$io = new Chat_IO();
		$response = $io->send('send', '', $xml);

		$kapenta->logEvent('chatclient', 'messagesout.set', 'send()::in', $response);
		$xd = new KXmlDocument($response);
		$root = $xd->getEntity(1);

		//------------------------------------------------------------------------------------------
		// ms - messages sent
		//------------------------------------------------------------------------------------------
		if ('ms' == $root['type']) {
			$children = $xd->getChildren();
			foreach($children as $childId) {
				$child = $xd->getEntity($childId);
				//----------------------------------------------------------------------------------
				// o - outgoing message accepted by server
				//----------------------------------------------------------------------------------
				if ('o' == $child['type']) {
					$model = new Chat_MessageOut($child['value']);
					if (true == $model->loaded) {
						$check = $model->delete();
						if (true == $check) {
							$logMsg = "*** rm sent message ". $model->UID ."<br/>\n";
							$kapenta->logEvent('chatclient', 'messagesout.set', 'send()::ms', $logMsg);
						} else { 
							$logMsg = "*** could not rm message " . $model->UID . " from queue";
							$kapenta->logEvent('chatclient', 'messagesout.set', 'send()::ms', $logMsg);
							$allOk = false;
						}
					}
				}
			}
		}

		//TODO: check for messages sent but not confirmed by server
		return $allOk;
	}

}

?>
