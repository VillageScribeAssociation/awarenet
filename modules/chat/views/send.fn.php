<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for sending a message to a chat room
//--------------------------------------------------------------------------------------------------

function chat_send($args) {
	global $user;
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('room', $args)) { return '(room not given)'; }
	$model = new Chat_Room($args['room']);
	if (false == $model->loaded) { return '(room not found)'; }

	//TODO: check that this user is a member of this room

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/chat/views/send.block.php');

	$labels = array(
		'room' => $model->UID,
		'fromUser' => $user->UID
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
