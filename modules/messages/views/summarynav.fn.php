<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarize a folder for the nav
//--------------------------------------------------------------------------------------------------
//opt: folder - folder to summarize (default is 'inbox') [string]
//opt: owner - UID of message owner (default is current user) [string]

function messages_summarynav($args) {
		global $kapenta;
		global $user;

	$owner = $user->UID;
	$folder = 'inbox';
	$html = '';
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (true == array_key_exists('owner', $args)) { $owner = $kapenta->db->addMarkup($args['owner']); }
	if (true == array_key_exists('folder', $args)) { $folder = $kapenta->db->addMarkup($args['folder']); }

	//----------------------------------------------------------------------------------------------
	//	count all messages
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "owner='" . $owner . "'";
	$conditions[] = "folder='" . $folder . "'";

	$totalMessages = $kapenta->db->countRange('messages_message', $conditions);

	//----------------------------------------------------------------------------------------------
	//	count unread messages
	//----------------------------------------------------------------------------------------------
	$conditions[] = "status='unread'";
	$newMessages = $kapenta->db->countRange('messages_message', $conditions);
	$unread = '';	

	if ('outbox' == $folder) { $newMessages = 'n/a'; }
	else { $unread = "<small>Unread: $newMessages messages</small><br/>\n"; }

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
