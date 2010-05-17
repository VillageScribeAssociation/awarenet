<?

	require_once($installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]

function schools_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new School($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/schools/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

