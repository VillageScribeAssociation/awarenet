<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or recordAlias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_revisionsnav($args) {
	if (authHas('projects', 'view', '') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['UID'] = $args['projectUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	load all revisions
	//----------------------------------------------------------------------------------------------

	$sql = "select * from projectrevisions "
		 . "where refUID='" . sqlMarkup($args['UID']) . "' order by editedOn";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);

		$revisionLink = '/projects/revision/' . $row['UID'];
		$revisionDate = date('Y-m-d', strtotime($row['editedOn']));

		$item = "$revisionDate by [[:users::name::userUID=" . $row['editedBy'] . ":]] ";
			 // . "<a href='" . $revisionLink . "'>[show]</a><hr/>";

		$html = $item . $html;
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

