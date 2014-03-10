<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count new messages in inbox
//--------------------------------------------------------------------------------------------------

function messages_inboxcount($args) {
	global $kapenta;

	if ('public' == $kapenta->user->role) { return '0'; }

	$cacheKey = 'pmcount::' . $kapenta->user->UID;

	//----------------------------------------------------------------------------------------------
	//	use memcached value if available
	//----------------------------------------------------------------------------------------------

	if ((true == $kapenta->mcEnabled) && (true == $kapenta->cacheHas($cacheKey))) {
		return $kapenta->cacheGet($cacheKey);
	}

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "folder='inbox'";
	$conditions[] = "status='unread'";
	$conditions[] = "owner='" . $kapenta->db->addMarkup($kapenta->user->UID) . "'";

	$newMessages = $kapenta->db->countRange('messages_message', $conditions);

	//----------------------------------------------------------------------------------------------
	//	cache the value for next time
	//----------------------------------------------------------------------------------------------

	if (true == $kapenta->mcEnabled) { $kapenta->cacheSet($cacheKey, $newMessages); }

	return $newMessages;

}


?>
