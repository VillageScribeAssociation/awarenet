<?

	require_once($kapenta->installPath . 'modules/projects/models/membership.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]

function projects_summary($args) {
	global $db, $theme;
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Projects_Project($db->addMarkup($args['raUID']));	
	$block = $theme->loadBlock('modules/projects/views/summary.block.php');
	return $theme->replaceLabels($model->extArray(), $block);
}

//--------------------------------------------------------------------------------------------------

?>

