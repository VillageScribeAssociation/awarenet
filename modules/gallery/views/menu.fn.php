<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu for gallery, no arguments
//--------------------------------------------------------------------------------------------------

function gallery_menu($args) {
	$labels = array();
	if (authHas('gallery', 'edit', '')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Create New Gallery::link=/gallery/new/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/gallery/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>