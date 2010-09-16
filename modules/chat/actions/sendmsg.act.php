<?

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');
	include $kapenta->installPath . 'modules/chat/inc/bots.inc.php';

//--------------------------------------------------------------------------------------------------
//	user has sent a message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make sure public users cannot send messages (spam)
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->doXmlError('please log in'); } 

	//----------------------------------------------------------------------------------------------
	//	OK, send the message
	//----------------------------------------------------------------------------------------------

	if ( (true == array_key_exists('toUser', $_POST))
		 AND (true == $db->objectExists('Users_User', $_POST['toUser']))
		 AND (true == array_key_exists('content', $_POST))
		) {

		$cleanContent = trim(strip_tags($_POST['content']));
		$cleanContent = str_replace("\r", '', $cleanContent);
		$cleanContent = str_replace("\n", '<br/>', $cleanContent);

		//------------------------------------------------------------------------------------------
		//	perform any bot actions
		//------------------------------------------------------------------------------------------
		
		$msgPair = chatBotsProcess($cleanContent, $toUser);

		//------------------------------------------------------------------------------------------
		//	recipients copy 
		//------------------------------------------------------------------------------------------

		//$recipient = new Chat($_POST['toUser']);
		//$msgUID = $recipient->createMsgUID();

		//$recipient->addMessage ('r' . $msgUID, 
		//						$user->UID, 
		//					 	$db->addMarkup($_POST['toUser']), 
		//						chatMarkup($msgPair['recipient']),
		//						'no');

		//$recipient->save();

		$msgUID = $kapenta->createUID();
		$b64Content = base64_encode($cleanContent);
		$msg = base64_encode($msgUID . '|' . $user->UID . '|' . $db->datetime() . '|' . time() . '|' . $b64Content . '|0');
		notifyChannel('chat-user-' . $_POST['toUser'], 'add', $msg);

		//------------------------------------------------------------------------------------------
		//	senders copy
 		//------------------------------------------------------------------------------------------

		//$sender = new Chat($user->UID);
		//$msgUID = $sender->createMsgUID();

		//$sender->addMessage('s' . $msgUID, 
		//					$db->addMarkup($_POST['toUser']), 
		//					$user->UID, 
		//					chatMarkup($msgPair['sender']), 
		//					'yes');

		//$sender->save();

		$msg = base64_encode($msgUID . '|' . $_POST['toUser'] . '|' . $db->datetime() . '|' . time() . '|' . $b64Content . '|1');
		notifyChannel('chat-user-' . $user->UID, 'add', $msg);

	}

?>
