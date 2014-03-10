<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a gallery [string]

function videos_editgalleryform($args) {
		global $theme;
		global $user;
		global $utils;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Videos_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $user->authHas('videos', 'videos_gallery', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['galleryTitle'] = $ext['title'];
	$ext['description64'] = $utils->b64wrap($ext['description']);
	$block = $theme->loadBlock('modules/videos/views/editgalleryform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
