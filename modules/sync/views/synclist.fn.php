<?

	require_once($installPath . 'modules/sync/models/servers.mod.php');
	require_once($installPath . 'modules/sync/models/sync.mod.php');

//--------------------------------------------------------------------------------------------------
//	summary list 
//--------------------------------------------------------------------------------------------------

function sync_synclist($args) {
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
			   . "<tr><td class='title'>Name</td>"
			   . "<td class='title'>Direction</td>"
			   . "<td class='title'>Active</td>"
			   . "<td class='title'>[x]</td><td class='title'>[x]</td></tr>\n";

		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

			$fullLink = "<a href='%%serverPath%%sync/syncwith/" . $row['UID'] . "'>[sync all]</a>";
			$dbLink = "<a href='%%serverPath%%sync/syncwith/" . $row['UID'] . "'>[sync database only]</a>";

			$html .= "\t<tr>\n"
				  . "\t\t<td class='wireframe'>" . $row['servername'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $row['direction'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $row['active'] . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $fullLink . "</td>\n"
				  . "\t\t<td class='wireframe'>" . $dbLink . "</td>\n"
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
