<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	show the edit form
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID or gallery entry

function gallery_editform($args) {
	if (authHas('gallery', 'edit', '') == false) { return false; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$c = new gallery($args['raUID']);
	return replaceLabels($c->extArray(), loadBlock('modules/gallery/views/editform.block.php'));
}


//--------------------------------------------------------------------------------------------------

?>