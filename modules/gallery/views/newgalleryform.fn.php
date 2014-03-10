<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to create a new gallery (formatted for nav, noargs)
//--------------------------------------------------------------------------------------------------

function gallery_newgalleryform($args) {
		global $user;
		global $theme;

	if ($user->authHas('gallery', 'gallery_gallery', 'new') == false) { return ''; }
	$block = $theme->loadBlock('modules/gallery/views/newgalleryform.block.php');
	return $block;
}

//--------------------------------------------------------------------------------------------------

?>
