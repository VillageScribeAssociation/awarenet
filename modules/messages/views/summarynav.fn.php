<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarize a folder for the nav
//--------------------------------------------------------------------------------------------------
//opt: folder - folder to summarize (default is 'inbox') [string]
//opt: owner - UID of message owner (default is current user) [string]

function messages_summarynav($args) {
	global $db, $user;
	$owner = $user->UID;
	$folder = 'inbox';
	$html = '';
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (true == array_key_exists('owner', $args)) { $owner = $db->addMarkup($args['owner']); }
	if (true == array_key_exists('folder', $args)) { $folder = $db->addMarkup($args['folder']); }

	//----------------------------------------------------------------------------------------------
	//	count all messages
	//----------------------------------------------------------------------------------------------
	$sql = "select count(UID) as allMsgs from Messages_Message "
		 . "where owner='" . $owner . "' and folder='" . $folder . "'";

	$result = $db->query($sql);
	$row = $db->fetchAssoc($result);
	$totalMessages = $row['allMsgs'];
	//TODO: $db->countRange

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------

	$sql = "select count(UID) as allMsgs from Messages_Message "
		 . "where owner='" . $owner . "' and folder='" . $folder . "' and status='unread'";

	$result = $db->query($sql);
	$row = $db->fetchAssoc($result);
	$newMessages = $row['allMsgs'];
	$unread = '';
	
	if ('outbox' == $folder) { $newMessages = 'n/a'; }
	else { $unread = "<small>Unread: $newMessages messages</small><br/><hr/>\n"; }

	//----------------------------------------------------------------------------------------------
	//	make HTML snippet
	//----------------------------------------------------------------------------------------------

	$html .= "<a href='%%serverPath%%messages/$folder/'><b>$folder</b></a><br/>\n"
		   . "<small>Total: $totalMessages messages</small><br/>\n"
		   . "$unread";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
