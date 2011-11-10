<?

//--------------------------------------------------------------------------------------------------
//|	makes a list of currently logged in / active users
//--------------------------------------------------------------------------------------------------

function users_activesessions($args) {
	global $user;
	global $theme;
	global $db;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	permission/role check
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query databse
	//----------------------------------------------------------------------------------------------
	$conditions = array("status='active'");
	$range = $db->loadRange('users_session', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();									//%	2d [array]
	$table[] = array('User', 'Server', 'Login');		//%
	foreach($range as $item) {
		$userLink = "[[:users::summarynav::userUID=" . $item['createdOn'] . ":]]";
		$serverLink = "<a href='" . $item['serverUrl'] . "'>" . $item['serverName'] . "</a>";
		$table[] = array($userLink, $serverLink, $item['createdOn']);
	}

	if (0 == count($range)) {
		$html .= "<div class='inlinequote'>No active user sessions.</div>";
	} else {
		$html .= $theme->arrayToHtmlTable($table, true, true);
	}

	return $html;
}

?>
