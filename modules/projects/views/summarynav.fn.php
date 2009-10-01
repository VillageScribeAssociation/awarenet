<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or projects entry
// * $args['projectUID'] = overrides raUID

function projects_summarynav($args) {
	if (array_key_exists('projectUID', $args) == true) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$g = new project(sqlMarkup($args['raUID']));	
	return replaceLabels($g->extArray(), loadBlock('modules/projects/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>