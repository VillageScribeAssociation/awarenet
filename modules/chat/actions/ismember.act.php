<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	discover if this user is a member of the given chat room
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chat_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { echo "<ban/>"; die(); }
	if ('' == $req->ref) { echo "<error>not logged in</error>"; die(); }

	$room = new Chat_Room($req->ref);
	if (false == $room->loaded) { echo "<error>not found</error>"; die(); }

	if (true == $room->memberships->has($user->UID)) { echo "<yes/>"; }
	else { echo "<no/>"; }

?>
