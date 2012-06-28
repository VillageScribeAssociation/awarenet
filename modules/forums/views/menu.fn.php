<?

//--------------------------------------------------------------------------------------------------
//|	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function forums_menu($args) {
	global $theme;
	global $user;

	$labels = array();			//%	block variables [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permission of current user to perform possible menu actions
	//----------------------------------------------------------------------------------------------
	$labels['newEntry'] = '[[:theme::submenu::label=Create New Forum::link=/forums/new/:]]';
	if (false == $user->authHas('forums', 'forums_board', 'new')) { $labels['newEntry'] = ''; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/forums/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
