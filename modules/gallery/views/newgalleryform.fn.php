<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to create a new gallery (formatted for nav, noargs)
//--------------------------------------------------------------------------------------------------

function gallery_newgalleryform($args) {
	if (authHas('gallery', 'edit', '') == false) { return false; }
	return loadBlock('modules/gallery/views/newgalleryform.block.php');
}

//--------------------------------------------------------------------------------------------------

?>