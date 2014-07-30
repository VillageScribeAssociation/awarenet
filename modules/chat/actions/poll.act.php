<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/inc/client.class.php');

//--------------------------------------------------------------------------------------------------
//*	look for and deliver any message for the current user in the current room
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and POST vars
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('Please log in.', true); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.', true); }
	
	if ('yes' != $kapenta->registry->get('chat.enabled')) { $page->doXmlError('Chat disabled.'); }

	$room = new Chat_Room($_POST['UID']);
	if (false == $room->loaded) { $page->do404('Chat room not found.', true); }

	//----------------------------------------------------------------------------------------------
	//	perform n checks for new messages
	//----------------------------------------------------------------------------------------------
	$checks = 1;
	if ($checks > 0) {
		$checks--;

		//----------------------------------------------------------------------------------------------
		//	query database
		//----------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "room='" . $db->addMarkup($room->UID) . "'";
		$conditions[] = "toUser='" . $db->addMarkup($user->UID) . "'";
		$conditions[] = "delivered='no'";
		$range = $db->loadRange('chat_inbox', '*', $conditions);

		//------------------------------------------------------------------------------------------
		//	return new messages
		//------------------------------------------------------------------------------------------
		foreach($range as $item) {
			//echo "<small>message: " . $item['UID'] . "</small><br/>\n";
			//echo $theme->expandBlocks('[[:chat::message::UID=' . $item['UID'] . ':]]', '');

			$fromUserBlock = "[[:users::name::userUID=" . $item['fromUser'] . ":]]";
			$fromUserName = $theme->expandBlocks($fromUserBlock, '');

			echo ''
			 .  "msg|" . $item['UID'] . "|" . $item['fromUser'] . "|"
			 . base64_encode($fromUserName) . "|"
			 . base64_encode($item['message']) . "\n";
		
			$model = new Chat_Inbox($item['UID']);
			$model->delivered = 'yes';
			$report = $model->save();
			if ('' != $report) { echo "<small>database error, retrying...</small><br/>"; }
			$checks = -1;	// finish immediately to return new message(s)
		}
	
		if ($checks > 0) { sleep(1); }

		//------------------------------------------------------------------------------------------
		//	set up another poll if none are ongoing
		//------------------------------------------------------------------------------------------
		$now = $kapenta->time();
		$lastpoll = $kapenta->registry->get('chat.lastcheck');
	
		if (($now - $lastpoll) > 10) {
			$lastpoll = $kapenta->registry->set('chat.lastcheck', $now);

			$client = new Chat_Client();
			$report = $client->check();

			if ('' != $report) { $kapenta->logEvent('chatclient', 'jspoll', 'report', $report); }
		}

	}

	//----------------------------------------------------------------------------------------------
	//	append member hash
	//----------------------------------------------------------------------------------------------
	echo "rmh|" . $room->memberships->rm() . "\n";
	//echo "<div class='chatmessageblack'><small>poll " . $kapenta->datetime() .  "</small></div>";
?>
