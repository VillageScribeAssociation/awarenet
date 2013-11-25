<?

//-------------------------------------------------------------------------------------------------
//*ku	add a message posted from the public 'contact us' form to administrator's mail queue
//-------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

	//---------------------------------------------------------------------------------------------
	//	authorization (TODO: captcha)
	//---------------------------------------------------------------------------------------------
	//anyone can post to the admins

	//---------------------------------------------------------------------------------------------
	//	verify that a message was posted
	//---------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('sendMessage' == $_POST['action'])) {
		$send = true;

		//-----------------------------------------------------------------------------------------
		//	get form vars  (TODO: lots of security checks)
		//-----------------------------------------------------------------------------------------
		$contactName = '';
		$contactEmail = '';
		$message = '';

		if (array_key_exists('contactName', $_POST)) { $contactName = $_POST['contactName']; }
		if (array_key_exists('contactEmail', $_POST)) { $contactEmail = $_POST['contactEmail']; }
		if (array_key_exists('message', $_POST)) { $message = $_POST['message']; }

		if ('' == trim($message)) { $send = false; }
		$message = $utils->stripHtml($message);
		$contactName = $utils->stripHtml($contactName);
		$contactEmail = $utils->stripHtml($contactEmail);

		$content = "Contact Name: " . $contactName . "<br/>\n"
				 . "Contact Email: " . $contactEmail . "<br/>\n"
				 . "Message:<br/><br/>" . str_replace("\n", "<br/>\n", $message) . "\n<br/><br/>\n"
				 . "<div class='inlinequote'><small><b>NOTE: This was posted through the public comment "
				 . "form by an unregistered user.</b></small></div>\n";

		//-----------------------------------------------------------------------------------------
		//	send to all admins
		//-----------------------------------------------------------------------------------------
		if (true == $send) {

			$conditions = array();
			$conditions[] = "role='admin'";
			$range = $db->loadRange('users_user', '*', $conditions, '', '', '');

			foreach($range as $row) {
				//---------------------------------------------------------------------------------
				//	add to inbox
				//---------------------------------------------------------------------------------
				$model = new Messages_Message();
				$model->owner = $row['UID'];
				$model->folder = 'inbox';
				$model->fromUID = 'public';
				$model->toUID = $row['UID'];
				$model->title = 'Public comment: ' . $contactName;
				$model->content = $content;
				$model->status = 'unread';
				$model->save();

				$_SESSION['sMessage'] .= "sent to " . $row['username'] . "<br/>\n";

				//---------------------------------------------------------------------------------
				//	send notification (TODO)
				//---------------------------------------------------------------------------------

			}

			$_SESSION['sMessage'] .= "Your message has been sent :-)<br/>\n";

		} else { $_SESSION['sMessage'] .= "Message was not sent.<br/>\n"; }


	} else { $_SESSION['sMessage'] .= "No message was posted.<br/>\n"; }

	//---------------------------------------------------------------------------------------------
	//	recirect back to home page (TODO: redirect back to wherever the user made this from)
	//---------------------------------------------------------------------------------------------
	$page->do302('');

?>
