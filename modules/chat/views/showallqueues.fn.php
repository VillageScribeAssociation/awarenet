<?

	require_once($installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//	display contents of all message queues (admin only, no arguments as yet)
//--------------------------------------------------------------------------------------------------

function chat_showallqueues($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$html = '';
	$sql = "select * from users order by surname, firstname";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$html .= "<h2>" . $row['surname'] . ', ' . $row['firstname'] . "</h2>\n";
		$html .= "<input type='button' onClick=\"cookieAddWindow('" . $row['UID'] . "', '100', '100'); cookieSetChatUpdate();\" "
				 . "value='chat' > (" . $row['UID'] . ")\n";
		$html .= "[[:chat::showqueue::user=" . $row['UID'] . ":]]<br/>\n";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>