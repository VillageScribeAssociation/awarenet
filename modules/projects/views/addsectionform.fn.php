<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for adding new sections
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of Projects_Project object [string]
//opt: UID - overrides raUID if present [string]
//opt: projectUID - overrides raUID if present [string]

function projects_addsectionform($args) {
	global $theme;
	global $user;
	global $db;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(project UID not given)'; }

	$model = new Projects_Project($args['raUID']);
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }

	if ('open' != $model->status) { return ''; }	// locked or closed

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/addsectionform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
