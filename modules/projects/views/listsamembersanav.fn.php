<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list recent projects in the nav
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a project [string]
//opt: projectUID - overrides UID [string]

function projects_listsamembersanav($args) {
	if (array_key_exists('projectUID', $args) == true) { $args['UID'] = $args['projectUID'];}	
	if (array_key_exists('UID', $args) == false) { return false;}	
	if (authHas('projects', 'view', '') == false) { return false; }
	$html = '';	$projects = array();

	$sql = "select * from projectmembers where projectUID='" . sqlMarkup($args['UID']) . "'";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$sql = "select * from projectmembers where userUID='" . $row['userUID'] . "'";
		$userresult = dbQuery($sql);
		while ($urow = dbFetchAssoc($userresult)) {
			$projects[$urow['projectUID']] = 'add';
		}
	}

	foreach($projects as $projectUID => $blah) {
		if ($projectUID != $args['UID']) {
			$html .= "[[:projects::summarynav::projectUID=" . $projectUID . ":]]\n";
		}
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

