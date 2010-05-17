<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]

function projects_show($args) {
	global $page;
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new project($args['raUID']);
	$page->blockArgs['projectTitle'] = $model->data['title'];
	return replaceLabels($model->extArray(), loadBlock('modules/projects/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

