<?

	require_once($kapenta->installPath . 'modules/chat/models/inbox.mod.php');

//--------------------------------------------------------------------------------------------------
//*	collection object for user inbox messages
//--------------------------------------------------------------------------------------------------

class Chat_Inboxes {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chat_Inboxes() {
		//TODO:
	}

	//----------------------------------------------------------------------------------------------
	//.	process as set of deliverable messages received from the server as XML
	//----------------------------------------------------------------------------------------------
	//arg: muXml - messages update XML document [string]
	//returns: true on success, false on failure [bool]

	function storeMessages($muXml) {
		$allOk = true;
		$xd = new KXmlDocument($muXml);
		$root = $xd->getEntity(1);
		if ('mu' != $root['type']) { return false; }

		$children = $xd->getChildren();
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('message' == $child['type']) {
				$pt = $xd->getChildren2d($childId);
				if (
					(false == array_key_exists('uid', $pt)) ||
					(false == array_key_exists('room', $pt)) ||
					(false == array_key_exists('fromuser', $pt)) ||
					(false == array_key_exists('touser', $pt)) ||
					(false == array_key_exists('message64', $pt))
				) {
					echo "*** invalid XML<br/>\n";
					$allOk = false;
				} else {
					$message = base64_decode($pt['message64']);
					$check = $this->storeMessage(
						$pt['uid'], $pt['room'], $pt['fromuser'], $pt['touser'], $message
					);
					if (false == $check) { $allOk = false; }
				}
			}
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	store a new message received by this peer
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Chat_Inbox object [string]
	//arg: room - UID of a Chat_Room object [string]
	//arg: fromUser - UID of a Users_User object [string]
	//arg: toUser - UID of a Users_User object [string]
	//arg: message - chat message to be delivered [string]
	//returns: true on success, false on failure [bool]

	function storeMessage($UID, $room, $fromUser, $toUser, $message) {
		//TODO: bunch of error checking here
		$model = new Chat_Inbox();
		$model->UID = $UID;
		$model->room = $room;
		$model->fromUser = $fromUser;
		$model->toUser = $toUser;
		$model->message = $message;
		$model->delivered = 'has';			//	'received' by this peer

		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	process confirmation from server of messages we have cached
	//----------------------------------------------------------------------------------------------
	//arg: mcXml - messages update XML document [string]
	//returns: true on success, false on failure [bool]

	function confirmMessages($mcXml) {
		$allOk = true;
		$xd = new KXmlDocument($mcXml);
		$root = $xd->getEntity(1);
		if ('mc' != $root['type']) { return false; }

		$children = $xd->getChildren();
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('m' == $child['type']) {
				$model = new Chat_Inbox($child['value']);
				if (true == $model->loaded) { 
					$model->delivered = 'no';
					$report = $model->save();
					if ('' == $report) {
						echo "*** server ack delivery of " . $model->UID . "<br/>\n";
					} else {
						echo "*** could not confirm reciept of " . $model->UID . "<br/>\n";
						$allOk = false;
					}
				}
			}
		}
		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an XML document describing messages received from the server but not yet delivered
	//----------------------------------------------------------------------------------------------
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment [string]

	function getHasXml($indent = '') {
		global $db;
		$conditions = array();
		$conditions[] = "delivered='has'";
		$range = $db->loadRange('chat_inbox', '*', $conditions);

		if (false === $range) { echo "*** database error getHasXml()<br/>\n"; return ''; }
		if (0 == count($range)) { return ''; }

		$xml = $indent . "<mh>\n";
		foreach($range as $item) {
			$xml .= $indent . "\t<m>" . $item['UID'] . "</m>\n";
		}
		$xml .= $indent . "</mh>\n";
		return $xml;
	}

}

?>
