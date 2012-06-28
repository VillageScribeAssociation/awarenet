<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Room object, formatted for nav
//--------------------------------------------------------------------------------------------------

function chat_addroomnav($args) {
	global $user, $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('chat', 'Chat_Room', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/chat/views/addroomnav.block.php');

	return $html;
}

?>
