<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for uploading multiple images
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of images per page

function images_uploadmultipleform($args) {
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'imageupload', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$labels = array('refModule' => $args['refModule'], 'refUID' => $args['refUID']);
	$html = replaceLabels($labels, loadBlock('modules/images/views/uploadmultiple.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>