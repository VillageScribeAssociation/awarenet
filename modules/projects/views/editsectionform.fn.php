<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing an article section
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//arg: sectionUID - UID of a section [string]

function projects_editsectionform($args) {
	global $theme;

	global $user;
	if ($user->authHas('projects', 'Projects_Project', 'edit', 'TODO:UIDHERE') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('sectionUID', $args) == false) { return false; }
	$model = new Projects_Project($args['raUID']);
	if ($model->isMember($user->UID) == false) { return false; }
	if (array_key_exists($args['sectionUID'], $model->sections) == false) { return false; }
	$labels = $model->sectionArray($args['sectionUID']);
	$labels['contentJs64'] = base64EncodeJs('contentJs64', $labels['content']);
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/projects/views/editsectionform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>