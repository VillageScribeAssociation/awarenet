<?

//--------------------------------------------------------------------------------------------------
//|	show a notice explaining that this project is locked
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - overrides raUID if present [string]

function projects_locked($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return '(project not found)'; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/locked.block.php');
	$labels = $model->extArray();
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}


?>
