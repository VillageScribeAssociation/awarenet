<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all (TODO: pagination, etc)
//--------------------------------------------------------------------------------------------------

function projects_listall($args) {
	global $db;
	$sql = "select * from projects_project order by createdOn DESC";
	$result = $db->query($sql);
	$html = '';
	while ($row = $db->fetchAssoc($result)) {
		$html .= "[[:projects::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

