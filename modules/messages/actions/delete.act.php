<?

//--------------------------------------------------------------------------------------------------
//	delete a message
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	   && ($_POST['action'] == 'deleteMessage') 
	   && (array_key_exists('UID', $_POST) == true) 
	   && (dbRecordExists('messages', $_POST['UID']) == true) ) {

		require_once($installPath . 'modules/messages/models/message.mod.php');

		$model = new Message($_POST['UID']);
		if ($model->data['owner'] != $user->data['UID']) { do404(); }

		//------------------------------------------------------------------------------------------
		//	delete the message
		//------------------------------------------------------------------------------------------

		$_SESSION['sMessage'] .= "Deleted message.<br/>\n";
		$model->delete();
		do302('messages/inbox/');

	} else { do404(); }

?>
