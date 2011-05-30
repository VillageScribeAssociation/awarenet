<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');
	require_once($kapenta->installPath . 'modules/sync/models/notice.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list 
//--------------------------------------------------------------------------------------------------
//TODO: use arrayToHtmlTable

function sync_serverlist($args) {
	global $db, $user, $theme;
	$html = '';						//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('sync_server', '*', '');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return "(no servers recorded, begin by adding own server)"; }

	$html .= "<table class='wireframe' width='100%'>\n"
		   . "<tr><td class='title'>Name</td><td class='title'>URL</td>"
		   . "<td class='title'>Direction</td><td class='title'>Active</td>"
		   . "<td class='title'>[x]</td><td class='title'>[x]</td></tr>\n";

	foreach ($range as $row) {
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
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
