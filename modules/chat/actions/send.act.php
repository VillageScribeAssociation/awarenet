<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/messageout.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/messagesout.set.php');

//--------------------------------------------------------------------------------------------------
//*	post a message for the central chat server
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { $page->doXmlError('403'); }
	if (false == array_key_exists('UID', $_POST)) { $page->doXmlError('Room not given.'); }
	if (false == array_key_exists('reqUID', $_POST)) { $page->doXmlError('reqUID not given.'); }
	if (false == array_key_exists('message', $_POST)) { $page->doXmlError('Message not given.'); }

	$room = new Chat_Room($_POST['UID']);
	if (false == $room->loaded) { $page->doXmlError('Unknown room.'); }

	$message = base64_decode($_POST['message']);
	$reqUID = substr($_POST['reqUID'], 0, 30);			//TODO: sanitize this, check for duplicates

	$kapenta->logEvent('chatclient', 'jssend', 'broadcast', $message);

	//----------------------------------------------------------------------------------------------
	//	add the outgoing message to the queue
	//----------------------------------------------------------------------------------------------
	$model = new Chat_MessageOut();
	$model->UID = $reqUID;
	$model->fromUser = $user->UID;
	$model->toRoom = $room->UID;
	$model->toUser = '*';
	$model->message = $message;
	$model->sent = $kapenta->datetime();
	$report = $model->save();

	$kapenta->logEvent('chatclient', 'jssend', 'log', $model->toXml());

	if ('' != $report) {
		$kapenta->logEvent('chatclient', 'jssend', 'log', "Could not send message (database error)");
		$page->doXmlError("Could not send message (database error)");
	}

	//----------------------------------------------------------------------------------------------
	//	try flush the queue up to the server
	//----------------------------------------------------------------------------------------------
	$set = new Chat_MessagesOut(true);
	if (true == $set->loaded) {
		$logMsg = "Sending to servers: " . count($set->members) . "\n";
		foreach($set->members as $item) { $logMsg .= $item['message'] . "\n"; }
		$kapenta->logEvent('chatclient', 'jssend', 'send', $logMsg);

		$check = $set->send();	// *****************************************************************
	
		if (true == $check) {
			$kapenta->logEvent('chatclient', 'jssend', 'send', "<sent>" . $model->UID . "</sent>");
			echo "<sent>" . $model->UID . "</sent>";
		} else {
			$kapenta->logEvent('chatclient', 'jssend', 'fail', "Could not pass message to chat server.");
			$page->doXmlError("Could not pass message to chat server.");
		}

	} else {
		$kapenta->logEvent('chatclient', 'jssend', 'send', "Could not load messagesout.");
	}
?>
