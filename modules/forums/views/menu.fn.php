<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function forums_menu($args) {
	$labels = array();
	if (authHas('forums', 'new', '') == true) {
		$labels['newEntry'] = '[[:theme::submenu::label=Create New Forum::link=/forums/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/forums/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
