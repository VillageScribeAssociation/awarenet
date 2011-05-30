<?

//--------------------------------------------------------------------------------------------------
//|	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function forums_menu($args) {
	global $theme, $user;

	$labels = array();
	if ($user->authHas('forums', 'forums_board', 'new') == true) {
		$labels['newEntry'] = '[[:theme::submenu::label=Create New Forum::link=/forums/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = $theme->replaceLabels($labels, $theme->loadBlock('modules/forums/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
