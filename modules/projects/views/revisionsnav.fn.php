<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or alias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_revisionsnav($args) {
	global $db;

	if ($user->authHas('projects', 'projects_project', 'show', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('projectUID', $args) == true) { $args['UID'] = $args['projectUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	load all revisions
	//----------------------------------------------------------------------------------------------

	$sql = "select * from projects_revision "
		 . "where refUID='" . $db->addMarkup($args['UID']) . "' order by editedOn";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

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
