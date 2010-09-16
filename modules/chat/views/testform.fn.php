<?

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display test message form (admins only, no arguments)
//--------------------------------------------------------------------------------------------------

function chat_testform($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return false; }
	return $theme->loadBlock('modules/chat/views/testaddmessage.block.php');
}

//--------------------------------------------------------------------------------------------------

?>