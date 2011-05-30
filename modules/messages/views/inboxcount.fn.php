<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count new messages in inbox
//--------------------------------------------------------------------------------------------------

function messages_inboxcount($args) {
	global $user, $db;
	if ('public' == $user->role) { return '0'; }

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "folder='inbox'";
	$conditions[] = "status='unread'";
	$conditions[] = "owner='" . $db->addMarkup($user->UID) . "'";

	$newMessages = $db->countRange('messages_message', $conditions);

	return $newMessages;

}


?>
