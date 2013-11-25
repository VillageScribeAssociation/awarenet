<?

	require_once($kapenta->installPath . 'modules/chatserver/models/outbox.mod.php');

//--------------------------------------------------------------------------------------------------
//*	collection object to handle undelivered / pending messages
//--------------------------------------------------------------------------------------------------

class Chatserver_Messages {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	//	none yet

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function Chatserver_Messages() {
		//TODO:
	}

	//----------------------------------------------------------------------------------------------
	//.	send a message to member(s) of a given room
	//----------------------------------------------------------------------------------------------
	//;	note that we are not checking the the user who sends the message is a member of the room,
	//;	this is to allow bots and admins to do broadcasts into chat rooms.  Clients should verify
	//;	this from the Javascript client, to prevent user spoofing.

	//arg: roomUID - UID of a Chatserver_Room object [string]
	//arg: fromUser - UID of a Users_User object [string]
	//arg: toUser - UID of a Users_User object [string]
	//arg: message - chat message to be delivered [string]
	//arg: sourceUID - UID of message in originating JS chat window [string]
	//returns: true on success, false on failure [bool]

	function send($roomUID, $fromUser, $toUser, $message, $sourceUID) {
		global $db;
		$allOk = true;									//%	return value [bool]
		$room = new Chatserver_Room($roomUID);			//%	[object:Chatserver_Room]

		if (false == $room->loaded) { return false; }
		if (false == $room->memberships->loaded) { $room->memberships->load(); }
		if (false == $room->memberships->loaded) { return false; }
		if (false == $db->objectExists('users_user', $fromUser)) { return false; }

		foreach($room->memberships->members as $item) {
			$addMember = true;

			if ('banned' == $item['role']) { $addMember = false; }
			if (('*' != $toUser) && ($item['user'] != $toUser)) { $addMember = false; }		// NB

			if (true == $addMember) {
				echo "<!-- adding for room member " . $item['user'] . " -->\n";

				$model = new Chatserver_Outbox();

				if ($fromUser == $item['user']) {
					$model->UID = $sourceUID;
					echo "<!-- source UID $sourceUID for $fromUser -->\n";
				}

				$model->room = $room->UID;
				$model->fromUser = $fromUser;
				$model->toUser = $item['user'];
				$model->message = $message;
				$model->delivered = 'no';
				$model->shared = 'no';
	
				$model->peer = $this->findPeerForUser($item['user']);

				$report = $model->save();
				if ('' == $report) {
					echo "<!-- recorded for user " . $item['user'] . " -->\n";
				} else {
					echo "<!-- could not record message for " . $item['user'] . " -->\n";
					$allOk = false;
				}

			} else { echo "<!-- not adding " .  $item['user'] . " ($toUser) -->\n"; }
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	find most recent user session
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User object [string]
	//returns: UID of a Chatserver_Peer object if active session found, empty string if not [string]

	function findPeerForUser($userUID) {
		global $db;
		$conditions = array();
		$conditions[] = "userUID='" . $db->addMarkup($userUID) . "'";
		$range = $db->loadRange('chatserver_session', '*', $conditions, 'createdOn ASC', '1');
		if (false === $range) { return ''; }

		foreach($range as $item) { return $item['serverUID']; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	update peer field for user (called when a new session is started)
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a Users_User obejct [string]
	//arg: peerUID - UID of a Chatserver_Peer object, or empty string [string]
	//returns: true on success, false on failure [bool]

	function setPeerForUser($userUID, $peerUID) {
		global $db;
		$allOk = true;								//%	return value [bool]
		$conditions = array();	
		$conditions[] = "toUser='" . $db->addMarkup($userUID) . "'";
		$conditions[] = "(delivered='no' OR delivered='peer')";

		$range = $db->loadRange('chatserver_outbox', '*', $conditions);
		
		foreach($range as $item) {
			$model = new Chatserver_Outbox();
			$model->loadArray($item);
			$model->peer = $peerUID;
			$model->delivered = 'no';
			$report = $model->save();
			if ('' != $report) { $allOk = false; }
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//.	load all pending/outgoing messages for a peer and serialize to XML
	//----------------------------------------------------------------------------------------------
	//arg: peerUID - UID of a Chatserver_Peer obejct [string]
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment or empty string if no outgoing messages [string]

	function outgoingToXml($peerUID, $indent = '') {
		global $db;
		$xml = '';											//%	return value [string]
		$conditions = array();

		$conditions[] = "peer='" . $db->addMarkup($peerUID) . "'";
		$conditions[] = "delivered='no'";
		$range = $db->loadRange('chatserver_outbox', '*', $conditions);

		if (false === $range) { return $indent . "<!-- db error on loading messages -->\n"; }
		if (0 == count($range)) { return ''; }

		$xml .= $indent . "<mu>\n";
		foreach($range as $item) {
			$xml .= ''
			 . $indent . "\t<message>\n"
			 . $indent . "\t\t<uid>" . $item['UID'] . "</uid>\n"
			 . $indent . "\t\t<room>" . $item['room'] . "</room>\n"
			 . $indent . "\t\t<fromuser>" . $item['fromUser'] . "</fromuser>\n"
			 . $indent . "\t\t<touser>" . $item['toUser'] . "</touser>\n"
			 . $indent . "\t\t<message64>" . base64_encode($item['message']) . "</message64>\n"
			 . $indent . "\t</message>\n";
			
		}
		$xml .= $indent . "</mu>\n";

		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	record 'mh' - messages the peer has received but not yet delivered to user
	//----------------------------------------------------------------------------------------------
	//arg: mhXml - XML document listing messages a peer has [string]
	//opt: indent - whitespace to indent XML by [string]
	//returns: XML fragment confirming receipt by client [string]

	function recordHas($mhXml, $indent = '') {
		$confirm = '';
		$xd = new KXmlDocument($mhXml);
		$root = $xd->getEntity(1);
		if ('mh' != $root['type']) { return ''; }

		$children = $xd->getChildren();
		foreach($children as $childId) {
			$child = $xd->getEntity($childId);
			if ('m' == $child['type']) {
				$model = new Chatserver_Outbox($child['value']);
				if (true == $model->loaded) {
					$model->delivered = 'peer';
					$report = $model->save();
					if ('' == $report) { $confirm .= $indent . "\t<m>" . $model->UID . "</m>\n"; }
					else { $confirm .= $indent . "\t<!-- fail " . $model->UID . " -->\n"; }
				}
			}
		}

		$confirm = $indent . "<mc>\n" . $confirm . $indent . "</mc>\n";
		return $confirm;
	}

}

?>
