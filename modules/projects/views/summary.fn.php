<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - overrides raUID if present [string]

function projects_summary($args) {
	global $db, $theme, $user;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Projects_Project($db->addMarkup($args['raUID']));	
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('projects', 'Projects_Project', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

