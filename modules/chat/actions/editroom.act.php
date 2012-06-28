<?
	//require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Room object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('Room not specified'); }
	$UID = $req->ref;
	if (false == $db->objectExists('chat_room', $UID)) { $page->do404(); }
	if (false == $user->authHas('chat', 'chat_room', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Rooms.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/chat/actions/editroom.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['roomUID'] = $UID;
	$page->render();

?>
