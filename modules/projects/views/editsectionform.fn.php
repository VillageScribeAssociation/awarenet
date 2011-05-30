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
	global $theme, $user, $utils;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	if (false == array_key_exists('sectionUID', $args)) { return ''; }
	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('projects', 'projects_project', 'edit', $model->UID)) { return ''; }
	if (false == array_key_exists($args['sectionUID'], $model->sections)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->sectionArray($args['sectionUID']);
	$labels['contentJs64'] = $utils->base64EncodeJs('contentJs64', $labels['content']);
	$block = $theme->loadBlock('modules/projects/views/editsectionform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
