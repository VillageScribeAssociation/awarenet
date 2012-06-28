<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/rooms.set.php');

//--------------------------------------------------------------------------------------------------
//*	join a chat room (can only join as member for now)
//--------------------------------------------------------------------------------------------------
//ref: room - UID of a Chat_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and POST vars
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { $page->doXmlError(''); }

	if ('' == $req->ref) { $page->doXmlError('room not specified'); }

	$room = new Chat_Room($req->ref);
	if (false == $room->loaded) { $page->doXmlError('room not found'); }

	//----------------------------------------------------------------------------------------------
	//	check if already a member
	//----------------------------------------------------------------------------------------------
	if (true == $room->memberships->has($user->UID)) { echo "<ok/>"; die(); }

	//----------------------------------------------------------------------------------------------
	//	make synchronous call to chat server
	//----------------------------------------------------------------------------------------------
	$set = new Chat_Rooms(true);
	$check = $set->join($room->UID, $user->UID, 'member');

	if (true == $check) {
		echo "<ok/>";	
	} else {
		$page->doXmlError("Could not join room: " . $room->title . " (" . $room->UID . ")");
	}

?>
