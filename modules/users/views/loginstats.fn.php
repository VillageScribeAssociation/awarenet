<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display list of users logged in within the last 1, 3 and 6 months
//--------------------------------------------------------------------------------------------------
//arg: startDate - datetime [string]
//arg: endDate - datetime [string]

function users_loginstats($args) {
		global $kapenta;
		global $theme;
		global $kapenta;

	$html = '';		//%	return value
	$startDate = $kapenta->db->datetime();
	$endDate = $kapenta->db->datetime();

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('startDate', $args)) { $startDate = $args['startDate']; }
	if (true == array_key_exists('endDate', $args)) { $endDate = $args['endDate']; }

	$startDate = str_replace("'", '', $startDate);	// TODO: better sanitization
	$endDate = str_replace("'", '', $endDate);

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$fields = 'UID, username, firstname, surname, role, lastOnline, alias';
	$conditions = array();
	$conditions[] = "CAST(lastOnline AS DATE) <= CAST('" . $startDate . "' AS DATE)";
	$conditions[] = "CAST(lastOnline AS DATE) > CAST('" . $endDate . "' AS DATE)";

	$range = $kapenta->db->loadRange('users_user', $fields, $conditions, 'CAST(lastOnline AS DATETIME)');
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('user', 'role', 'last online');

	foreach($range as $row) {
		$userUrl = '%%serverPath%%users/profile/' . $row['alias'];
		$userLink = "<a href='$userUrl'>" . $row['firstname'] . ' ' . $row['surname'] . "</a>";
		$table[] = array($userLink, $row['role'], $row['lastOnline']);
	}

	$html .= "<b>Users: " . count($range) . " Start Date: $startDate End Date: $endDate</b><br/>\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);
	$html .= "<br/>\n";

	return $html;
}


?>
