<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	show a record
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or schools entry

function schools_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new School($args['raUID']);
	return replaceLabels($model->extArray(), loadBlock('modules/schools/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>