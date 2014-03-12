<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a mail item
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check the user is authorised to view the message
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->do403('Please log in to view messages.'); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Message not specified (UID).'); }

	$model = new Messages_Message($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Message not found.'); }
	if ($model->owner != $kapenta->user->UID) { $kapenta->page->do403('You cannot view this message.'); }

	//----------------------------------------------------------------------------------------------
	//	mark as read
	//----------------------------------------------------------------------------------------------
	if ('unread' == $model->status) {	
		$model->status = 'read';
		$model->save(); 
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/messages/actions/show.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['folder'] = $model->folder;
	$kapenta->page->blockArgs['owner'] = $model->owner;
	$kapenta->page->render();

?>
