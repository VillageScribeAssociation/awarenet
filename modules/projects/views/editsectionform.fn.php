<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for editing an article section
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or projects entry
// * $args['sectionUID'] = UID of a section

function projects_editsectionform($args) {
	global $user;
	if (authHas('projects', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	if (array_key_exists('sectionUID', $args) == false) { return false; }
	$model = new Project($args['raUID']);
	if ($model->isMember($user->data['UID']) == false) { return false; }
	if (array_key_exists($args['sectionUID'], $model->sections) == false) { return false; }
	$labels = $model->sectionArray($args['sectionUID']);
	$labels['contentJs64'] = base64EncodeJs('contentJs64', $labels['content']);
	return replaceLabels($labels, loadBlock('modules/projects/views/editsectionform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
