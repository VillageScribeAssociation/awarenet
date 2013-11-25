<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]

function projects_summarynav($args) {
	global $db;
	global $cache;
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check cache
	//----------------------------------------------------------------------------------------------
	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	//TODO: permission check

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new Projects_Project($db->addMarkup($args['raUID']));
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	fill out the block template and return it
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/summarynav.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('projects-summarynav-' . $model->UID, $args['area'], $args['rawblock'], $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
