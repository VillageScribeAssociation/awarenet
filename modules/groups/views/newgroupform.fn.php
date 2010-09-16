<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new group, shown in nav
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - UID of the school this group belongs to [string]

function groups_newgroupform($args) {
	global $user, $theme;
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('groups', 'Groups_Group', 'new')) { return ''; }
	if (false == array_key_exists('schoolUID', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = array('schoolUID' => $args['schoolUID']);
	$block =$theme->loadBlock('modules/groups/views/newgroupform.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
