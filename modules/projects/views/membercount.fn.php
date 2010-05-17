<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	return number of members in project
//--------------------------------------------------------------------------------------------------
//arg: projectUID - UID pf project to count members for [string]

function projects_membercount($args) {
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }

	$sql = "select count(UID) as memberCount from projectmembers "
		 . "where projectUID='" . $args['projectUID'] . "' and role != 'asked'";

	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
	return $row['memberCount'];
}

//--------------------------------------------------------------------------------------------------

?>

