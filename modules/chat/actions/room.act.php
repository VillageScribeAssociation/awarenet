<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	contents of chat room iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403('[[::users::pleaselogin:]]', true); }

	if ('' == $kapenta->request->ref) { $page->do404('Room not specified.', true); }
	
	$model = new Chat_Room($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Room not found.', true); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/chat/actions/room.if.page.php');
	$kapenta->page->blockArgs['currentUserUID'] = $user->UID;

	$ext = $model->extArray();
	foreach($ext as $key => $value) { $kapenta->page->blockArgs[$key] = $value; }
	$kapenta->page->render();

?>
