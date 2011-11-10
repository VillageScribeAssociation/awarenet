<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/inc/diff.inc.php');

//--------------------------------------------------------------------------------------------------
//|	list revisions made to a project
//--------------------------------------------------------------------------------------------------
//arg: UID - UID or alias of a project [string]
//opt: projectUID - overrides raUID [string]

function projects_revisionsnav($args) {
	global $kapenta;
	global $user;
	global $db;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['UID'] = $args['projectUID']; }
	if (false == array_key_exists('UID', $args)) { return false; }

	$model = new Projects_Project($args['UID']);
	if (false == $model->loaded) { return 'Project not found.'; }

	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { 
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//	load all revisions from database
	//----------------------------------------------------------------------------------------------
	//TODO: update this to use collection object of section revisions
	$sql = "select * from projects_revision "
		 . "where refUID='" . $db->addMarkup($args['UID']) . "' order by editedOn";

	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);

		$revisionLink = '/projects/revision/' . $row['UID'];
		$revisionDate = date('Y-m-d', $kapenta->strtotime($row['editedOn']));

		$item = "$revisionDate by [[:users::name::userUID=" . $row['editedBy'] . ":]] ";
			 // . "<a href='" . $revisionLink . "'>[show]</a><hr/>";

		$html = $item . $html;
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
