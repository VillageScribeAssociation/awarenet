<?

	require_once($kapenta->installPath . 'modules/chatserver/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/chatserver/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	client report a user quit membership of a chat room
//--------------------------------------------------------------------------------------------------
//+	Example message (base64 encoded):
//+
//+		<membership>
//+			<room>1234ROOMUID890</room>
//+			<user>0987USERUID321</user>
//+		</membership>

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
		$page->doXmlError('Unknown peer.');
	}

	//----------------------------------------------------------------------------------------------
	//	process message (consider moving this to room/rooms object)
	//----------------------------------------------------------------------------------------------

	$xd = new KXmlDocument($msg);
	$root = $xd->getEntity(1);

	$roomUID = '';
	$userUID = '';
	$chatrole = 'member';

	echo "<!-- root entity type: " . $root['type'] . " -->\n";
	if ('membership' == $root['type']) {
		$children = $xd->getChildren();			//%	handles to children of root entity [array:int]
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			switch(strtolower($child['type'])) {
				case 'room':	$roomUID = $child['value']; 	break;
				case 'user':	$userUID = $child['value']; 	break;
			}
		}
	}

	if ('' == $roomUID) { $page->doXmlError('room UID not found'); }
	if ('' == $userUID) { $page->doXmlError('user UID not found'); }

	if (false == $db->objectExists('users_user', $userUID)) { $page->doXmlError('Unkown user.'); }

	$model = new Chatserver_Room($roomUID);
	if (false == $model->loaded) { $page->doXmlError('Unknown chat room.'); }

	//----------------------------------------------------------------------------------------------
	//	remove the member and try to update count on room object
	//----------------------------------------------------------------------------------------------
	$check = $model->memberships->remove($userUID);

	$model->memberCount = $model->memberships->count();
	$model->save();

	if (true == $check) {
		echo "<!-- removed user from chat room " . $model->UID . " -->\n";
		echo "<ok/>\n";
	} else {
		echo "<!-- could not remove user from chat room -->\n";
	}

?>
