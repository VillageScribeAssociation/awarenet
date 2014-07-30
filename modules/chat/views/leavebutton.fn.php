<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a button to redirect to the 'leave room' syncronous chat action
//--------------------------------------------------------------------------------------------------
//arg: roomUID - UID of a chat_room object [string]

function chat_leavebutton($args) {
	global $user;
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('roomUID', $args)) { return '(no roomUID)'; }

	$model = new Chat_Room($args['roomUID']);
	if (false == $model->loaded) { return '(unknown room)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/chat/views/leavebutton.block.php');
	$labels = array('roomUID' => $model->UID);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}


?>
