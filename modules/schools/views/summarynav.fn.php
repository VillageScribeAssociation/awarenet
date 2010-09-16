<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for nav (300 px wide)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID or schools entry [string]
//opt: schoolUID - overrides raUID [string]

function schools_summarynav($args) {
	global $db;

	global $theme;

	if (array_key_exists('schoolUID', $args) == true) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new Schools_School($db->addMarkup($args['raUID']));	
	return $theme->replaceLabels($c->extArray(), $theme->loadBlock('modules/schools/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>