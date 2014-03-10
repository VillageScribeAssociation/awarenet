<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery record, required [string]

function gallery_summarynav($args) {
	global $kapenta;
	global $theme;
	global $cache;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check the cache
	//----------------------------------------------------------------------------------------------
	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('galleryUID', $args)) { return ''; }
	$model = new Gallery_Gallery($args['galleryUID']);	
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['galleryUID'] = $args['galleryUID'];
	$block = $theme->loadBlock('modules/gallery/views/summarynav.block.php');

	$html = $theme->replaceLabels($labels, $block);
	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('gallery-summarynav-' . $model->UID, $args['area'], $args['rawblock'], $html);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
