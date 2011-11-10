<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add group members
//--------------------------------------------------------------------------------------------------
//arg: groupUID - overrides raUID [string]
//arg: userUID - UID of a Users_User object [string]

function groups_addmemberformjs($args) {
	global $theme;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('groupUID', $args)) 
		{ return "<span class='ajaxerror'>Missing group UID.</span>"; }

	if (false == array_key_exists('userUID', $args)) 
		{ return "<span class='ajaxerror'>Missing user UID.</span>"; }

	$model = new Groups_Group($args['groupUID']);
	if (false == $model->loaded) { return "<span class='ajaxerror'>Unkown group: " . $args['groupUID'] . "</span>"; }

	if ($model->hasMember($args['userUID'])) { 
		return "<span class='ajaxwarn'>This person is already a member.</span>"; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/groups/views/addmemberformjs.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
