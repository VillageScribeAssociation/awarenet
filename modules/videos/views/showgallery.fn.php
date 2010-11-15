<?

	require_once($kapenta->installPath . 'modules/videos/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a video gallery
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Gallery object [string]
//opt: galleryUID - overrides raUID of present [string]

function videos_showgallery($args) {
	global $theme;
	$html = '';				//% return value;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('galleryUID', $args)) { $args['raUID'] = $args['galleryUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }
	$model = new Videos_Gallery($args['raUID']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/videos/views/showgallery.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
