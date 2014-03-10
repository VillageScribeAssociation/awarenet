<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a message
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteMessage' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Message not specified (UID)'); }

	$model = new Messages_Message($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Message not found.'); }
	if ($model->owner != $user->UID)
		{ $kapenta->page->do403('Not authorized to delete this message.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the message
	//----------------------------------------------------------------------------------------------
	$session->msg("Deleted message.", 'ok');
	$model->delete();
	$kapenta->page->do302('messages/inbox/');

?>
