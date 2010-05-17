<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show a record
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a gallery [string]

function gallery_show($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$s = new gallery($args['raUID']);
	return replaceLabels($s->extArray(), loadBlock('modules/gallery/views/show.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>

