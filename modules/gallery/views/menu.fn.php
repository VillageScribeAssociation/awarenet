<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for gallery, no arguments
//--------------------------------------------------------------------------------------------------

function gallery_menu($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$labels = array();

	$labels['newEntry'] = '[[:theme::submenu::label=Create New Gallery::link=/gallery/new/:]]';
	if (false == $kapenta->user->authHas('gallery', 'gallery_gallery', 'edit')) { $labels['newEntry'] = ''; }
	
	$block = $theme->loadBlock('modules/gallery/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
