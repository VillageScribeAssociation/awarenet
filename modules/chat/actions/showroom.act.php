<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	 action to display a single Room object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('No Room specified.'); } 
	$model = new Chat_Room($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404("Unkown Room");}

//	if (false == $user->authHas('chat', 'chat_room', 'show', $model->UID)) {
//		$page->do403('You are not authorized to view this Room.'); 
//	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/chat/actions/showroom.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['roomUID'] = $model->UID;
	//	^ add any further block arguments here
	$kapenta->page->render();

?>
