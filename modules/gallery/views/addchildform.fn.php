<?

	require_once($kapenta->installPath . 'modules/gallery/models/gallery.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a child page - not currently used in awareNet
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a gallery [string]

function gallery_addchildform($args) {
	global $theme;

	if (array_key_exists('UID', $args) == false) { return false; }
	$html = '';
	$labels = array('UID' => $args['UID']);
	return $theme->replaceLabels($labels, $theme->loadBlock('modules/gallery/views/addchild.block.php'));	
}

//--------------------------------------------------------------------------------------------------

?>