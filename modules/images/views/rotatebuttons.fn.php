<?

//--------------------------------------------------------------------------------------------------
//|	creates buttons for rotating an image by 90 degress clockwise or anticlockwise
//--------------------------------------------------------------------------------------------------
//arg: imageUID - UID of an Images_Image object [string]

function images_rotatebuttons($args) {
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: check permissions

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/images/views/rotatebuttons.block.php');
	$labels = $args;

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}


?>
