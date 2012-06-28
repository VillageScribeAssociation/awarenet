<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a Videos_Gallery object [string]

function videos_summarynav($args) {
	global $db;
	global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('galleryUID', $args)) { return ''; }
	$model = new Videos_Gallery($args['galleryUID']);	
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$labels['galleryUID'] = $args['galleryUID'];
	$block = $theme->loadBlock('modules/videos/views/summarynav.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
