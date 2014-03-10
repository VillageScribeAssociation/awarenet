<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all projects
//--------------------------------------------------------------------------------------------------

function projects_listall($args) {
	global $kapenta;
	$sql = "select * from projects_project order by createdOn DESC";
	$result = $kapenta->db->query($sql);
	$html = '';
	while ($row = $kapenta->db->fetchAssoc($result)) {
		$html .= "[[:projects::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

