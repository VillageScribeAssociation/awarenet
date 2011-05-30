<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for gallery, no arguments
//--------------------------------------------------------------------------------------------------

function videos_menu($args) {
	global $db, $user, $theme;
	$labels = array();

	$labels['newEntry'] = '[[:theme::submenu::label=Create New Gallery::link=/gallery/new/:]]';
	if (false == $user->authHas('videos', 'videos_gallery', 'new')) { $labels['newEntry'] = ''; }
	
	$block = $theme->loadBlock('modules/videos/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
