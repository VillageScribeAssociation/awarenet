<?

	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//*	delete a forums thread (and all replies)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('deleteThread' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Thread not specified (UID).'); }

	$model = new Forums_Thread($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404('Thread not found.'); }
	if (false == $user->authHas('forums', 'forums_thread', 'delete', $model->UID)) 
		{ $kapenta->page->do403('You are not permitted to delete this thread.'); }

	//----------------------------------------------------------------------------------------------
	//	delete the thread and redirect back to forum
	//----------------------------------------------------------------------------------------------  
	$forumUID = $model->board;
	$model->delete();
	$session->msg("Deleted forum thread: " . $model->title, 'ok');
	$kapenta->page->do302('forums/' . $forumUID);

?>
