<?

	require_once($installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]

function schools_summary($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new School(sqlMarkup($args['raUID']));	
	return replaceLabels($c->extArray(), loadBlock('modules/schools/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

