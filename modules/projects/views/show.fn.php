<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show project content
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: UID - overrides raUID if present [string]
//opt: projectUID - overrides raUID if present [string]

function projects_show($args) {
		global $theme;
		global $kapenta;
		global $user;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(UID not given)'; }

	$model = new Projects_Project($args['raUID']);
	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	$block = $theme->loadBlock('modules/projects/views/show.block.php');
	$kapenta->page->blockArgs['projectTitle'] = $model->title;

	$labels = $model->extArray();
	$labels['addSectionForm'] = '[[:projects::addsectionform::projectUID=' . $model->UID . ':]]';

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
