<?

	require_once($installPath . 'modules/chat/models/chat.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all users with links to start a chat
//--------------------------------------------------------------------------------------------------

function chat_listallbegin($args) {
	$html = '';
	$sql = "select * from users order by ofGroup";
	$result = dbQuery($sql);

	$html .= "<table noborder>\n";
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$chatLink = "<a href='/chat/begin/" . $row['UID'] . "'>[begin chat]</a>";

		$html .= "\t<tr>\n";
		$html .= "\t\t<td>" . $row['username'] . "</td>\n";
		$html .= "\t\t<td>" . $row['ofGroup'] . "</td>\n";
		$html .= "\t\t<td>" . $chatLink . "</td>\n";
		$html .= "\t</tr>\n";
	}
	$html .= "</table>";
	return $html;
}


?>
