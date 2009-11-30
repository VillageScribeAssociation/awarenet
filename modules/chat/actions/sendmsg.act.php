<?

//--------------------------------------------------------------------------------------------------
//	user has sent a message
//--------------------------------------------------------------------------------------------------

	include $installPath . 'modules/chat/inc/bots.inc.php';

	//----------------------------------------------------------------------------------------------
	//	make sure public users cannot send messages (spam)
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] == 'public') { doXmlError('please log in'); } 

	//----------------------------------------------------------------------------------------------
	//	OK, send the message
	//----------------------------------------------------------------------------------------------
	require_once($installPath . 'modules/chat/models/chat.mod.php');

	if ( (array_key_exists('toUser', $_POST))
		 AND (dbRecordExists('users', $_POST['toUser']))
		 AND (array_key_exists('content', $_POST))
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
		//						$user->data['UID'], 
		//					 	sqlMarkup($_POST['toUser']), 
		//						chatMarkup($msgPair['recipient']),
		//						'no');

		//$recipient->save();

		$msgUID = createUID();
		$b64Content = base64_encode($cleanContent);
		$msg = base64_encode($msgUID . '|' . $user->data['UID'] . '|' . mysql_datetime() . '|' . time() . '|' . $b64Content . '|0');
		notifyChannel('chat-user-' . $_POST['toUser'], 'add', $msg);

		//------------------------------------------------------------------------------------------
		//	senders copy
 		//------------------------------------------------------------------------------------------

		//$sender = new Chat($user->data['UID']);
		//$msgUID = $sender->createMsgUID();

		//$sender->addMessage('s' . $msgUID, 
		//					sqlMarkup($_POST['toUser']), 
		//					$user->data['UID'], 
		//					chatMarkup($msgPair['sender']), 
		//					'yes');

		//$sender->save();
		$msg = base64_encode($msgUID . '|' . $_POST['toUser'] . '|' . mysql_datetime() . '|' . time() . '|' . $b64Content . '|1');
		notifyChannel('chat-user-' . $user->data['UID'], 'add', $msg);


	}

?>
