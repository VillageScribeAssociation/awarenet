<?

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display contents of all message queues (admin only, no arguments as yet)
//--------------------------------------------------------------------------------------------------

function chat_showallqueues($args) {
	global $db;

	global $user;
	if ('admin' != $user->role) { return false; }

	$html = '';
	$sql = "select * from Users_User order by surname, firstname";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "<h2>" . $row['surname'] . ', ' . $row['firstname'] . "</h2>\n";
		$html .= "<input type='button' onClick=\"cookieAddWindow('" . $row['UID'] . "', '100', '100'); cookieSetChatUpdate();\" "
				 . "value='chat' > (" . $row['UID'] . ")\n";
		$html .= "[[:chat::showqueue::user=" . $row['UID'] . ":]]<br/>\n";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
