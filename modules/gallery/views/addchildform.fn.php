<?

	require_once($installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to add a child page
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID of gallery entry

function gallery_addchildform($args) {
	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';
	$labels = array('UID' => $args['UID']);
	return replaceLabels($labels, loadBlock('modules/gallery/views/addchild.block.php'));	
}

//--------------------------------------------------------------------------------------------------

?>