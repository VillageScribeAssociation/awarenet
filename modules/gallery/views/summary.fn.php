<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	summarise
//--------------------------------------------------------------------------------------------------
// * $args['pageUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID of gallery page

function gallery_summary($args) {
	if (array_key_exists('pageUID', $args)) { $args['raUID'] = $args['pageUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new gallery(sqlMarkup($args['raUID']));	
	return replaceLabels($c->extArray(), loadBlock('modules/gallery/views/summary.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>