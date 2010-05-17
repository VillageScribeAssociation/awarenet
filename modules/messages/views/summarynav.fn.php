<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarize a folder for the nav
//--------------------------------------------------------------------------------------------------
//opt: folder - folder to summarize (default is 'inbox') [string]
//opt: owner - UID of message owner (default is current user) [string]

function messages_summarynav($args) {
	global $user;
	$owner = $user->data['UID']; $folder = 'inbox'; $html = '';
	if ('public' == $user->data['ofGroup']) { return false; }
	if (array_key_exists('owner', $args) == true) { $owner = sqlMarkup($args['owner']); }
	if (array_key_exists('folder', $args) == true) { $folder = sqlMarkup($args['folder']); }

	//----------------------------------------------------------------------------------------------
	//	count all messages
	//----------------------------------------------------------------------------------------------

	$sql = "select count(UID) as allMsgs from messages "
		 . "where owner='" . $owner . "' and folder='" . $folder . "'";

	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
	$totalMessages = $row['allMsgs'];

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------

	$sql = "select count(UID) as allMsgs from messages "
		 . "where owner='" . $owner . "' and folder='" . $folder . "' and status='unread'";

	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
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

