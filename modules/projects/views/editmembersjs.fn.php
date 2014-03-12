<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	AJAX front end for editing project memebrships
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - overrides raUID if present [string]

function projects_editmembersjs($args) {
	global $theme;
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }	
	if (false == array_key_exists('raUID', $args)) { return '(project not specified)'; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return '(unkown project)'; }

	$listOnly = '[[:projects::listmembersnav::projectUID=' . $model->UID. '::editmode=none:]]';

	if ('open' != $model->status) { return $listOnly; }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'editmembers', $model->UID)) {
		return $listOnly;
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/editmembersjs.block.php');

	$labels = $model->extArray();
	$labels['projectUID'] = $model->UID;

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}


?>
