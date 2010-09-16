<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list 
//--------------------------------------------------------------------------------------------------

function sync_synclist($args) {
	global $db, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user is an admin
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('Sync_Server', '*', '', 'direction');
	//$sql = "select * from servers order by direction";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return "(no servers recorded, begin by adding own server)"; }

	$html .= "<table class='wireframe' width='100%'>\n"
		   . "<tr><td class='title'>Name</td>"
		   . "<td class='title'>Direction</td>"
		   . "<td class='title'>Active</td>"
		   . "<td class='title'>[x]</td><td class='title'>[x]</td></tr>\n";

	foreach ($range as $row) {
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

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
