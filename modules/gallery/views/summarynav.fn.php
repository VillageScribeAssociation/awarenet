<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summarise for the nav (300 wide)
//--------------------------------------------------------------------------------------------------
//arg: galleryUID - UID of a gallery record, required [string]

function gallery_summarynav($args) {
	if (array_key_exists('galleryUID', $args) == false) { return false; }
	$model = new Gallery(sqlMarkup($args['galleryUID']));	
	$labels = $model->extArray();
	$labels['galleryUID'] = $args['galleryUID'];
	return replaceLabels($labels, loadBlock('modules/gallery/views/summarynav.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

