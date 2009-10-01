<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for nav (300 px wide)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or schools entry
// * $args['schoolUID'] = overrides raUID

function schools_summarynav($args) {
	if (array_key_exists('schoolUID', $args) == true) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new School(sqlMarkup($args['raUID']));	
	return replaceLabels($c->extArray(), loadBlock('modules/schools/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>