<?

//--------------------------------------------------------------------------------------------------
//|	display a table showing all active section locks, admins only
//--------------------------------------------------------------------------------------------------

function projects_showalllocks($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $theme;
	global $user;

	$lockTimeout = 600;		//%	ten minutes, TODO: make this a registry setting [int]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("lockedBy != ''");
	$range = $kapenta->db->loadRange('projects_section', '*', $conditions, 'projectUID');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Project', 'Section', 'Lock Owner', 'Expires', '[x]');

	foreach ($range as $item) {
		$bp = '[[:projects::linkobject';
		$projectLink = $bp . '::type=projects_project::UID=' . $item['projectUID'] . ':]]';
		$sectionLink = $bp . '::type=projects_section::UID=' . $item['UID'] . ':]]';
		$userLink = '[[:users::namelink::userUID=' . $item['lockedBy'] . ':]]';

		$lockTimestamp = $kapenta->strtotime($item['lockedOn']);
		$expires = $kapenta->datetime($lockTimestamp + $lockTimeout);

		$clearUrl = '%%serverPath%%projects/showlocks/clear_' . $item['UID'] . '/';
		$clearLink = "<a href='" . $clearUrl . "'>[clear]</a>";

		$table[] = array($projectLink, $sectionLink, $userLink, $expires, $clearLink);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);

	$cronTimestamp = $kapenta->registry->get('cron.tenmins');
	$cronDateTime = $kapenta->datetime((int)$cronTimestamp);
	$html .= "<b>Most recent cron:</b> " . $cronDateTime . " (" . $cronTimestamp . ")<br/>";

	return $html;
}

?>
