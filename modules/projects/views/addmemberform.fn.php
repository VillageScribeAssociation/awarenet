<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to add project members
//--------------------------------------------------------------------------------------------------
// * $args['projectUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or projects entry

function projects_addmemberform($args) {
	if (array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$labels = array('raUID' => $args['raUID']);	
	return replaceLabels($labels, loadBlock('modules/projects/views/addmemberform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>