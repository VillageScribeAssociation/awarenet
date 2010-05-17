<?

	require_once($installPath . 'modules/sync/models/server.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list 
//--------------------------------------------------------------------------------------------------

function sync_serverlist($args) {
	$html = '';
	if (authHas('sync', 'list', '') == false) { return false; }
	if (authHas('sync', 'view', '') == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------

	$sql = "select * from servers order by direction";
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {

		$html .= "<table class='wireframe' width='100%'>\n"
			   . "<tr><td class='title'>Name</td><td class='title'>URL</td>"
			   . "<td class='title'>Direction</td><td class='title'>Active</td>"
			   . "<td class='title'>[x]</td><td class='title'>[x]</td></tr>\n";

		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

			$editLink = "<a href='%%serverPath%%sync/editserver/" . $row['UID'] . "'>[edit]</a>";
			$delLink = "<a href='%%serverPath%%sync/delserver/" . $row['UID'] . "'>[del]</a>";

			$html .= "\t<tr>\n"
				  . "\t\t<td class='wireframe'>" . $row['servername'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $row['serverurl'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $row['direction'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $row['active'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $editLink . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $delLink . "</td>\n"
				  . "\t</tr>\n";
		}

		$html .= "</table>\n";

	} else {
		$html .= "(no servers recorded, begin by adding own server)";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
