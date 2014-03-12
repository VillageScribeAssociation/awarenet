<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the edit form
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]

function gallery_editform($args) {
		global $theme;
		global $kapenta;
		global $utils;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Gallery_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('gallery', 'gallery_gallery', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$ext = $model->extArray();
	$ext['description64'] = $utils->b64wrap($ext['description']);
	$block = $theme->loadBlock('modules/gallery/views/editform.block.php');
	$html = $theme->replaceLabels($ext, $block);
	return $html;
}


//--------------------------------------------------------------------------------------------------

?>
