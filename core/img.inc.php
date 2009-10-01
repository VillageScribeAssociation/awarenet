<?

//--------------------------------------------------------------------------------------------------
//	utility functions for working with images
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	convert a hex color to RGB
//--------------------------------------------------------------------------------------------------

function imgHexToRgb($hex) {
	$rgb = array('r' => 0, 'g' => 0, 'b' => 0);
	$hex = str_replace('#', '', trim($hex));
	if (strlen($hex) != 6) { return $rgb; }
	$rgb['r'] = hexdec(substr($hex, 0, 2));
	$rgb['g'] = hexdec(substr($hex, 2, 2));
	$rgb['b'] = hexdec(substr($hex, 4, 2));
	return $rgb;
}

?>
