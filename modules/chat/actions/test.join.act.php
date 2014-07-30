<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/models/rooms.set.php');

//--------------------------------------------------------------------------------------------------
//*	development/test action to join a room as current user
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chat_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $page->do403(); }

	$set = new Chat_Rooms(true);

	if ('' == $kapenta->request->ref) {
		echo "Please choose a chat room:<br/>\n";
		foreach($set->members as $item) {
			if ('global' == $item['state']) {
				echo $item['UID'] . ' - ' . $item['title'] . "<br/>\n";
			}
		}
		die();
	}

	$model = new Chat_Room($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Chat room not found.'); }

	//----------------------------------------------------------------------------------------------
	//	add the current user as a member of the room
	//----------------------------------------------------------------------------------------------

	if (true == $model->memberships->has($user->UID)) {
		echo "You are already a member of this room.<br/>\n";
	} else {
		$check = $set->join($model->UID, $user->UID, 'member');
		if (true == $check) {
			echo "You have joined room: " . $model->title . " (" . $model->UID .")<br/>\n";
		} else {
			echo "Could not joined room: " . $model->title . " (" . $model->UID . ")<br/>\n";
		}
	}

?>
