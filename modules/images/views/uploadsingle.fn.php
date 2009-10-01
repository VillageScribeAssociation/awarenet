<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	display upload/view for a single image (eg, user profile picture)
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = name of a module
// * $args['refUID'] = record which owns this image
// * $args['category'] = category of image, eg userprofile

function images_uploadsingle($args) {	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	$category = ''; $width = '300';
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('category', $args) == false) { $category = $args['category']; }
	if (array_key_exists('width', $args) == false) { $width = $args['width']; }
	if (authHas($args['refModule'], 'imageupload', $args) == 0) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add block
	//----------------------------------------------------------------------------------------------
	$labels = array();
	$labels['refModule'] = $args['refModule'];
	$labels['refUID'] = $args['refUID'];

	$html = replaceLabels($labels, loadBlock('modules/images/views/uploadsingle.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>