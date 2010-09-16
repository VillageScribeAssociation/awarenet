<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for groups, no arguments
//--------------------------------------------------------------------------------------------------

function groups_menu($args) {
	global $theme, $user;
	$labels = array();
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments (if any) and permissions
	//----------------------------------------------------------------------------------------------

	$labels['newEntry'] = '';
	if (true == $user->authHas('groups', 'Groups_Group', 'edit')) 
		{ $labels['newEntry'] = '[[:theme::submenu::label=Add Group::link=/groups/new/:]]';	}

	$labels['editCurrentGroup'] = '';
	$labels['viewCurrentGroup'] = '';

	if ((true == array_key_exists('editGroupUrl', $args)) && ($args['editGroupUrl'] != '')) { 
			$labels['editCurrentGroup'] = "[[:theme::submenu::label=Edit This Group" 
										  . "::link=" . $args['editGroupUrl'] . ":]]";
	}

	if (true == array_key_exists('viewGroupUrl', $args)) { 
			$labels['viewCurrentGroup'] = "[[:theme::submenu::label=View This Group" 
										  . "::link=" . $args['viewGroupUrl'] . ":]]";
	}

	//----------------------------------------------------------------------------------------------
	//	make and return the block
	//----------------------------------------------------------------------------------------------	
	$block = $theme->loadBlock('modules/groups/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
