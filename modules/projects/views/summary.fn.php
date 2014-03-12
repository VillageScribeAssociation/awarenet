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
	global $kapenta;
	global $theme;
	global $kapenta;
	global $session;
	global $cache;

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check block cache
	//----------------------------------------------------------------------------------------------
	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($kapenta->db->addMarkup($args['raUID']));	
	if (false == $model->loaded) { return ''; }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make and cache the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/summary.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('projects-summary-' . $model->UID, $args['area'], $args['rawblock'], $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

