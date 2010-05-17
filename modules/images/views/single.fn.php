<?

	require_once($installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a single image (eg, user profile picture)
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a module [string]
//arg: refUID - record which owns this image [string]
//opt: category - category of image, eg userprofile [string]
//opt: size - size of image (default is width300) [string]

function images_single($args) {
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	$category = ''; $size = 'width300';
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	if (array_key_exists('category', $args) == true) { $category = $args['category']; }
	if (array_key_exists('size', $args) == true) { $size = $args['size']; }

	//----------------------------------------------------------------------------------------------
	//	find the image matching this space
	//----------------------------------------------------------------------------------------------
	$im = new Image();
	$imgUID = $im->findSingle($args['refModule'], $args['refUID'], $category);

	if ($imgUID == false) { 
		$html = "<img src='/themes/clockface/unavailable/" . $size . ".jpg' />";
	} else {
		$html = "<img src='/images/" . $size . "/" . $im->data['recordAlias'] . "' />";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

