<?

	require_once($kapenta->installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all users with links to start a chat
//--------------------------------------------------------------------------------------------------

function chat_listallbegin($args) {
	global $db;

	$html = '';
	$sql = "select * from users_user order by role";
	$result = $db->query($sql);

	$html .= "<table noborder>\n";
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$chatLink = "<a href='/chat/begin/" . $row['UID'] . "'>[begin chat]</a>";

		$html .= "\t<tr>\n";
		$html .= "\t\t<td>" . $row['username'] . "</td>\n";
		$html .= "\t\t<td>" . $row['role'] . "</td>\n";
		$html .= "\t\t<td>" . $chatLink . "</td>\n";
		$html .= "\t</tr>\n";
	}
	$html .= "</table>";
	return $html;
}


?>
