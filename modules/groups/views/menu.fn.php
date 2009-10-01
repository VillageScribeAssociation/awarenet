<?

	require_once($installPath . 'modules/groups/models/groups.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu for groups, no arguments
//--------------------------------------------------------------------------------------------------

function groups_menu($args) {
	$labels = array();
	if (authHas('groups', 'edit', '')) { $labels['newEntry'] = '[[:theme::submenu::label=Add Group::link=/groups/new/:]]';	}
	else { $labels['newEntry'] = ''; }

	$labels['editCurrentGroup'] = '';
	$labels['viewCurrentGroup'] = '';

	if ((array_key_exists('editGroupUrl', $args)) && ($args['editGroupUrl'] != '')) { 
			$labels['editCurrentGroup'] = "[[:theme::submenu::label=Edit This Group" 
										  . "::link=" . $args['editGroupUrl'] . ":]]";
	}

	if (true == array_key_exists('viewGroupUrl', $args)) { 
			$labels['viewCurrentGroup'] = "[[:theme::submenu::label=View This Group" 
										  . "::link=" . $args['viewGroupUrl'] . ":]]";
	}
	
	$html = replaceLabels($labels, loadBlock('modules/groups/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>