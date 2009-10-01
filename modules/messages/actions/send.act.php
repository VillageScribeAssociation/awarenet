<?

//--------------------------------------------------------------------------------------------------
//	send a message
//--------------------------------------------------------------------------------------------------

	// recipients field is a string of user UIDs separated by pipes

	//----------------------------------------------------------------------------------------------
	//	public user cannot send messages
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->data['ofGroup']) { do403(); }
	require_once($installPath . 'modules/messages/models/message.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check submitted form
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	   && ($_POST['action'] == 'sendMessage')
	   && (array_key_exists('recipients', $_POST) == true) ) {

		//------------------------------------------------------------------------------------------
		//	make array of all recipients
		//------------------------------------------------------------------------------------------

		$cc = array();
		$recips = explode("|", $_POST['recipients']);
		foreach($recips as $toUID) 
			{ if ((strlen($toUID) > 3) && dbRecordExists('users', $toUID)) { $cc[] = $toUID; } }

		//------------------------------------------------------------------------------------------
		//	send to all recipients
		//------------------------------------------------------------------------------------------

		foreach ($cc as $toUID) {

			//------------------------------------------------------------------------------------------
			//	recipient's copy
			//------------------------------------------------------------------------------------------

			$subject = clean_string($_POST['subject']);
			if (trim($subject) == '') { $subject = '(no subject)'; }

			$model = new Message();
			$model->data['owner'] = $toUID;
			$model->data['folder'] = 'inbox';
			$model->data['fromUID'] = $user->data['UID'];
			$model->data['toUID'] = $toUID;
			$model->data['cc'] = implode('|', $cc);
			$model->data['title'] = $subject;
			$model->data['content'] = $_POST['content'];
			$model->data['status'] = 'unread';
			$model->save();

			//------------------------------------------------------------------------------------------
			//	sender's copy
			//------------------------------------------------------------------------------------------

			$model->data['UID'] = createUID();
			$model->data['owner'] = $user->data['UID'];
			$model->data['folder'] = "outbox";
			$model->save();
		}

		//------------------------------------------------------------------------------------------
		//	redirect back to inbox
		//------------------------------------------------------------------------------------------
		
		do302('messages/inbox/');

	} else { do404(); }

?>
