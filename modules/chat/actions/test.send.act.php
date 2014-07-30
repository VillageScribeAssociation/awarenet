<?

	require_once($kapenta->installPath . 'modules/chat/models/messageout.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/messagesout.set.php');

//--------------------------------------------------------------------------------------------------
//*	test / development action to send a message to a chat room
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Send Test Message (synchronous)</h1>\n";

	//----------------------------------------------------------------------------------------------
	//	send message if POSTed
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('action', $_POST)) {
		$roomUID = $_POST['room'];
		$message = $_POST['message'];

		$mo = new Chat_MessageOut();
		$mo->toRoom = $roomUID;
		$mo->fromUser = $user->UID;
		$mo->toUser = '*';
		$mo->message = $message;
		//$check = $mo->send();

		$report = $mo->save();

		if ('' == $report) { echo "<div class='chatmessagegreen'>Message queued.</div>\n"; }
		else { echo "<div class='chatmessagered'>Message not saved to queue.</div>\n"; }

		echo "<div class='chatmessageblack'>\n";
		$set = new Chat_MessagesOut();
		$check = $set->send();
		echo "</div>\n";

		if (true == $check) { echo "<div class='chatmessagegreen'>Messages Sent</div>\n"; }
		else { echo "<div class='chatmessagered'>Error while sending to server.</div>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//	show new message form
	//----------------------------------------------------------------------------------------------
	$html = "
	<div class='chatmessageblack'>
	<form name='sendTest' method='POST'>
	<input type='hidden' name='action' value='sendMessage' />
	<b>Room:</b> [[:chat::selectroom:]] 
	<b>From:</b> [[:users::namelink::userUID=" . $user->UID . ":]]
	<br/>
	<textarea rows='10' cols='80' name='message'></textarea><br/>\n
	<input type='submit' value='Send'>
	</form>
	</div>
	";

	echo $theme->expandBlocks($html, '');
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
