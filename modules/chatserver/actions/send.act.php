<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/messages.set.php');

//--------------------------------------------------------------------------------------------------
//*	note a new message to be sent to chat room members
//--------------------------------------------------------------------------------------------------
//+	Example message (base64 encoded):
//+
//+		<message>
//+			<uid>3434SOURCEUID3434</uid>
//+			<room>1234ROOMUID890</room>
//+			<fromtuser>0987USERUID321</fromuser>
//+			<touser>*</touser>
//+			<message64>BASE64-ENCODED-MESSAGE</message64>
//+		</message>
//+
//+	Note that until private chat messages are enabled the touser field should always be '*'
//+	The source UID is used when confirming the message in the originating JS client

	//----------------------------------------------------------------------------------------------
	//	check client signature and arguments
	//----------------------------------------------------------------------------------------------
	//TODO: check signature here

	if (false == array_key_exists('me', $_POST)) { $page->doXmlError('Peer not identified.'); }
	if (false == array_key_exists('msg', $_POST)) { $page->doXmlError('Msg not given.'); }

	$peerUID = $_POST['me'];
	$msg = base64_decode($_POST['msg']);

	$peer = new Chatserver_Peer($peerUID, true);
	if (false == $peer->loaded) {
		$kapenta->logEvent('chatserver', 'send', 'fail', 'Unknown peer.');
		$page->doXmlError('Unknown peer.');
	}

	//----------------------------------------------------------------------------------------------
	//	process messages (consider moving this to room/rooms object)
	//----------------------------------------------------------------------------------------------
	$allOk = true;
	$xd = new KXmlDocument($msg);
	$root = $xd->getEntity(1);
	$response = '';

	$response .= "\t<!-- root entity type: " . $root['type'] . " -->\n";
	if ('mn' != $root['type']) { $page->doXmlError('Incorrect root entity type.'); }

	$children = $xd->getChildren();			//%	handles to children of root entity [array:int]

	foreach($children as $childId) {
		$child = $xd->getEntity($childId);

		if ('message' == $child['type']) {
			//--------------------------------------------------------------------------------------
			//	process 'message' entity
			//--------------------------------------------------------------------------------------
			$parts = $xd->getChildren2d($childId);

			if (false == array_key_exists('uid', $parts)) { $page->doXmlError('Bad XML.'); }
			if (false == array_key_exists('room', $parts)) { $page->doXmlError('Bad XML.'); }
			if (false == array_key_exists('fromuser', $parts)) { $page->doXmlError('Bad XML.'); }
			if (false == array_key_exists('touser', $parts)) { $page->doXmlError('Bad XML.'); }
			if (false == array_key_exists('message64', $parts)) { $page->doXmlError('Bad XML.'); }

			$UID = $parts['uid'];
			$roomUID = $parts['room'];
			$fromUser = $parts['fromuser'];
			$toUser = $parts['touser'];
			$message = base64_decode($parts['message64']);

			//--------------------------------------------------------------------------------------
			//	check parts
			//--------------------------------------------------------------------------------------
			if (false == $db->objectExists('users_user', $fromUser)) {
				$page->doXmlError('Unknown user.');
			}

			$room = new Chatserver_Room($roomUID);
			if (false == $room->loaded) { $page->doXmlError('Unknown chat room.'); }

			//--------------------------------------------------------------------------------------
			//	send to member(s) of the chat room
			//--------------------------------------------------------------------------------------
			$set = new Chatserver_Messages();
			$check = $set->send($room->UID, $fromUser, $toUser, $message, $UID);

			if (true == $check) {
				$response .= "\t<!-- sent to chat room " . $room->UID . " -->\n";
				$response .= "\t<o>$UID</o>\n";
			} else {
				$response .= "\t<!-- could not send to to chat room -->\n"; $allOk = false;
			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<ms>\n" . $response . "</ms>\n";		// ms - messages sent

?>
