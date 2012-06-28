<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	creates a link to a chat room (or a label if user not logged in)
//--------------------------------------------------------------------------------------------------
//arg: roomUID - UID of a Chat_Room object [string]

function chat_roomlink($args) {
	global $user;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('roomUID', $args)) { return '(UID not given)'; }

	$model = new Chat_Room($args['roomUID']);
	if (false == $model->loaded) { return '(chat room not found)'; }

	if (('public' == $user->role) || ('banned' == $user->role)) { return $model->title; }

	$url = $kapenta->serverPath . "chat/showroom/" . $model->UID;
	$html = "<a href='$url'>" . $model->title . "</a>";

	return $html;
}


?>
