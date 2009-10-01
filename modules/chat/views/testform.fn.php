<?

	require_once($installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//	display test message form (admins only, no arguments)
//--------------------------------------------------------------------------------------------------

function chat_testform($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }
	return loadBlock('modules/chat/views/testaddmessage.block.php');
}

//--------------------------------------------------------------------------------------------------

?>