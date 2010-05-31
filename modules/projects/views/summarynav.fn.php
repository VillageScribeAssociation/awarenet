<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or projects entry [string]
//opt: projectUID - overrides raUID [string]

function projects_summarynav($args) {
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	//TODO: permission check

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------
	$model = new project(sqlMarkup($args['raUID']));
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	fill out the block template and return it
	//----------------------------------------------------------------------------------------------
	$block = loadBlock('modules/projects/views/summarynav.block.php');
	$html = replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
