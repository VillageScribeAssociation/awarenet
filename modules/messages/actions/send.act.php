<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	send a message
//--------------------------------------------------------------------------------------------------
// recipients field is a string of user UIDs separated by pipes

	//----------------------------------------------------------------------------------------------
	//	public user cannot send messages
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check submitted form
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('sendMessage' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('recipients', $_POST)) { $page->do404('Recipients unspecified.'); }

	//----------------------------------------------------------------------------------------------
	//	make array of all recipients
	//----------------------------------------------------------------------------------------------
	$cc = array();
	$recips = explode("|", $_POST['recipients']);
	foreach($recips as $toUID) 
		{ if ((strlen($toUID) > 3) && $db->objectExists('Users_User', $toUID)) { $cc[] = $toUID; } }
	//TODO: tidy

	//----------------------------------------------------------------------------------------------
	//	send to all recipients
	//----------------------------------------------------------------------------------------------

	foreach ($cc as $toUID) {

		//------------------------------------------------------------------------------------------
		//	recipient's copy
		//------------------------------------------------------------------------------------------
		$subject = $utils->cleanString($_POST['subject']);
		if ('' == trim($subject)) { $subject = '(no subject)'; }

		$model = new Messages_Message();
		$model->owner = $toUID;
		$model->folder = 'inbox';
		$model->fromUID = $user->UID;
		$model->data['toUID'] = $toUID;
		$model->cc = implode('|', $cc);
		$model->title = $subject;
		$model->content = $_POST['content'];
		$model->status = 'unread';
		$model->save();

		//------------------------------------------------------------------------------------------
		//	sender's copy
		//------------------------------------------------------------------------------------------

		$model->UID = $kapenta->createUID();
		$model->owner = $user->UID;
		$model->folder = "outbox";
		$model->save();
	}

	//------------------------------------------------------------------------------------------
	//	redirect back to inbox
	//------------------------------------------------------------------------------------------
		
	$page->do302('messages/inbox/');

?>
