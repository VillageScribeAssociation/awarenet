<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all (TODO: pagination, etc)
//--------------------------------------------------------------------------------------------------

function projects_listall($args) {
	$sql = "select * from projects order by createdOn DESC";
	$result = dbQuery($sql);
	$html = '';
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:projects::summary::raUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

