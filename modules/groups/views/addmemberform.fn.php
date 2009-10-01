<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to add group members
//--------------------------------------------------------------------------------------------------
// * $args['groupUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or groups entry

function groups_addmemberform($args) {
	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$labels = array('raUID' => $args['raUID']);	
	return replaceLabels($labels, loadBlock('modules/groups/views/addmemberform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>