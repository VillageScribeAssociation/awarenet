<?
	//require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Room object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Room not specified'); }
	$UID = $kapenta->request->ref;
	if (false == $db->objectExists('chat_room', $UID)) { $page->do404(); }
	if (false == $user->authHas('chat', 'chat_room', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Rooms.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/chat/actions/editroom.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['roomUID'] = $UID;
	$kapenta->page->render();

?>
