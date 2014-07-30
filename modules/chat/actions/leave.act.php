<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');
	require_once($kapenta->installPath . 'modules/chat/inc/io.class.php');

//--------------------------------------------------------------------------------------------------
//*	leave a chat room (can only leave as self - not kick others)
//--------------------------------------------------------------------------------------------------
//postarg: roomUID - UID of a chat_room object [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('roomUID', $_POST)) { $page->do404('Room UID not given.'); }

	$model = new Chat_Room($_POST['roomUID']);
	if (false == $model->loaded) { $page->do404("Unknown room."); }
	
	// check that this user is a member of this room
	if (false == $model->memberships->has($user->UID)) {
		$page->do404('You are not a member of this room.');
	}

	//----------------------------------------------------------------------------------------------
	//	call to chat server
	//----------------------------------------------------------------------------------------------
	$msg = ''
	 . "<membership>\n"
	 . "\t<room>" . $model->UID . "</room>\n"
	 . "\t<user>" . $user->UID . "</user>\n"
	 . "</membership>\n";

	$io = new Chat_IO();
	$response = $io->send('leave', '', $msg);

	//echo "<pre>" . htmlentities($response) . "</pre>"
	echo "
	<html>
	<head>
	</head>
	<body onLoad=\"window.location = '" . $kapenta->serverPath . "chat/';\">
	Leaving...
	</body>
	</html>
	";

?>
