<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]

function schools_show($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Schools_School($args['raUID']);
	return $theme->replaceLabels($model->extArray(), $theme->loadBlock('modules/schools/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>