<?

//--------------------------------------------------------------------------------------------------
//*	object to handle communications with the central chat server
//--------------------------------------------------------------------------------------------------

class Chat_IO {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	var $server = '';				//_	URL of chat server [string]
	var $myUID = '';				//_	UID of this peer [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chat_IO() {
		global $kapenta;
		$this->server = $kapenta->registry->get('chat.server');
		$this->myUID = $kapenta->registry->get('p2p.server.uid');
	}

	//----------------------------------------------------------------------------------------------
	//.	send a message / request to the central chat server
	//----------------------------------------------------------------------------------------------
	//arg: action - name of an action on the chatserver module [string]
	//opt: ref - reference to pass action [string]
	//opt: msg - message to post [string]

	function send($action, $ref = '', $msg = '') {
		global $utils;
		global $kapenta;

		// disable proxy if chat server and client are the same
		$oldProxyVal = $kapenta->proxyEnabled;
		if ($this->server == $kapenta->serverPath) { $kapenta->proxyEnabled = 'no'; }

		$checkUrl = $this->server . 'chatserver/' . $action . '/' . $ref;
		echo "send: " . $checkUrl . "<br/>\n";
		$params = array('me' => $this->myUID, 'msg' => base64_encode($msg));
		$response = $utils->curlPost($checkUrl, $params);
		$kapenta->proxyEnabled = $oldProxyVal;
		return $response;
	}

}

?>
