<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for downloading multiple images
//--------------------------------------------------------------------------------------------------
// * $args['refModule'] = module to list on
// * $args['refUID'] = number of images per page

function images_downloadmultipleform($args) {
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'images', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	add the form
	//----------------------------------------------------------------------------------------------
	$labels = array('refModule' => $args['refModule'], 'refUID' => $args['refUID']);
	$html = replaceLabels($labels, loadBlock('modules/images/views/downloadmultiple.block.php'));
	
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>