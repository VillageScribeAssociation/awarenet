<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//	count new messages in inbox
//--------------------------------------------------------------------------------------------------

function messages_inboxcount($args) {
	global $user;
	if ('public' == $user->data['ofGroup']) { return '0'; }

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------

	$sql = "select count(UID) as allMsgs from messages "
		 . "where owner='" . $user->data['UID'] . "' and folder='inbox' and status='unread'";

	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
	$newMessages = $row['allMsgs'];

	return $newMessages;

}


?>