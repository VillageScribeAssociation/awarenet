<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a Room object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Chat_Room object [string]//opt: roomUID - UID of a Chat_Room object, overrides UID [string]
function chat_showroom($args) {
	global $user;
	global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('roomUID', $args)) { $raUID = $args['roomUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Chat_Room($raUID);	//% the object we're editing [object:Chat_Room]

	if (false == $model->loaded) { return ''; }
	//if (false == $user->authHas('chat', 'chat_room', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/chat/views/showroom.block.php');
	$labels = $model->extArray();
	$labels['memberCount'] = $model->memberships->count();
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
