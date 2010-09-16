<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show a mail item
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check the user is authorised to view the message
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('Please log in to view messages.'); }
	if ('' == $req->ref) { $page->do404('Message not specified (UID).'); }

	$model = new Messages_Message($req->ref);
	if (false == $model->loaded) { $page->do404('Message not found.'); }
	if ($model->owner != $user->UID) { $page->do403('You cannot view this message.'); }

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
	$page->load('modules/messages/actions/show.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['folder'] = $model->folder;
	$page->blockArgs['owner'] = $model->owner;
	$page->render();

?>
