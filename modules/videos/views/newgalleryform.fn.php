<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to create a new video gallery (formatted for nav, noargs)
//--------------------------------------------------------------------------------------------------

function videos_newgalleryform($args) {
		global $kapenta;
		global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('videos', 'videos_gallery', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/videos/views/newgalleryform.block.php');
	$html = $theme->ntb($html, 'Create New Gallery', 'divNewVideoGallery', 'hide');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
