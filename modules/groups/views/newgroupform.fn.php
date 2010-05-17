<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new group, shown in nav
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of the school this group belongs to [string]

function groups_newgroupform($args) {
	if (authHas('groups', 'edit', '') == false) { return false; }
	if (array_key_exists('schoolUID', $args) == false) { return false; }
	$labels = array('schoolUID' => $args['schoolUID']);
	return replaceLabels($labels, loadBlock('modules/groups/views/newgroupform.block.php'));
}


//--------------------------------------------------------------------------------------------------

?>

