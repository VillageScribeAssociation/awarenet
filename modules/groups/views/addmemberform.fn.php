<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add group members
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a group [string]
//opt: groupUID - overrides raUID [string]

function groups_addmemberform($args) {
	global $theme;

	if (array_key_exists('groupUID', $args)) { $args['raUID'] = $args['groupUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$labels = array('raUID' => $args['raUID']);	
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/groups/views/addmemberform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>