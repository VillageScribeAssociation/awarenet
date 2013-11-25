<?

	require_once($kapenta->installPath . 'modules/chatserver/inc/server.class.php');
	require_once($kapenta->installPath . 'modules/chatserver/inc/hashes.class.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/session.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action through which clients assert their state and request same from this server
//--------------------------------------------------------------------------------------------------
//+
//+	Key to chat message lines which may be sent by server:
//+
//+		GENERAL MESSAGES
//+
//+			### [comment]			- comment
//+			er: [msg]				- error
//+
//+		NETWORK INFORMATION
//+
//+			nk:	hash				- sha1 hash of network (all peers)
//+			nc: please				- asks peer to empty it's peers table/cache
//+			np: uid|name|url		- assert this network peer exists
//+			nh: please				- ask peer to recalculate it's network hash
//+
//+		CHAT ROOMS
//+
//+			rx:	UID|title|desc		- asserts chat room exists globally
//+			rl:	[hashlist]			- asserts hashes or rooms and room memberships
//+
//+	Key to chat message lines which may be sent by client:
//+
//+			### [comment]			- comment
//+			er: [msg]				- error
//+			me: UID					- peer identifying itself
//+			nk:	[hash]				- assert sha1 hash of network (all peers)
//+	
//+		USER SESSIONS
//+
//+			sb: [user.UID]			- session begin (user logged in) 
//+			se: [user.UID]			- session end (user logged out)
//+			sc: please				- client requests we clear all their sessions
//+			sx:	[userUID]			- client asserts a session exists for this user
//+			sk:	[userUID]			- client requests we recalculate their sessions hash
//+
//+		CHAT ROOM
//+
//+			ra: [hash]				- asserts a rooms-all hash, aggregate state of all chat rooms
//+			rx:	UID|title|desc		- client asserts chat room exists locally


	$cycles = 10;		//%	max number of cycles [int]
	$checkTime = 3;		//%	wait time between checking hashes / messages, seconds [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and signature
	//----------------------------------------------------------------------------------------------
	//TODO: signature checking here

	if (false == array_key_exists('me', $_POST)) { die("<cs><er>Peer not iedentified.</er></cs>"); }
	if (false == array_key_exists('msg', $_POST)) { die("<cs><er>No message posted.</er></cs>\n"); }

	$peerUID = $_POST['me'];
	$msg = base64_decode($_POST['msg']);

	$server = new Chatserver_Server($peerUID);
	if (false == $server->peer->loaded) { die('<cs><er>Unknown peer: ' . $peerUID . '</er></cs>'); }

	$server->process($msg);

	$kapenta->logEvent('chatserver', 'check.act.php', 'recieved message', $msg);

	//----------------------------------------------------------------------------------------------
	//	send response when server has something to say
	//----------------------------------------------------------------------------------------------

	while (('' == trim($server->response)) && ($cycles > 0)) {
		$server->checkHashes();
		$server->checkMessages();

		if ('' != $server->response) {
			break;
			$kapenta->logEvent('chatserver', 'check.act.php', 'breaking sleep cycle', $cycles);
			die();
		}

		$cycles--;
		$kapenta->logEvent('chatserver', 'check.act.php', 'cycle ' . $cycles, '(sleep(3), nothing to send)');
		sleep($checkTime);
	}

	$kapenta->logEvent('chatserver', 'check.act.php', 'reply', $server->response);
	$server->sendResponse();

?>
