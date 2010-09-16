<?

//--------------------------------------------------------------------------------------------------
//*	utility functions for working with images deprecated  TODO: move to utils global object
//--------------------------------------------------------------------------------------------------
//+ these should be replaced by blocks on the images module, or moved to images/inc/

//--------------------------------------------------------------------------------------------------
//|	convert a hex color to RGB
//--------------------------------------------------------------------------------------------------
//arg: hex - six digit HTML color, optionally with # [string]
//returns: array of three integers r, g, b [array]

function imgHexToRgb($hex) {
	$rgb = array('r' => 0, 'g' => 0, 'b' => 0);
	$hex = str_replace('#', '', trim($hex));
	if (strlen($hex) != 6) { return $rgb; }
	$rgb['r'] = hexdec(substr($hex, 0, 2));
	$rgb['g'] = hexdec(substr($hex, 2, 2));
	$rgb['b'] = hexdec(substr($hex, 4, 2));
	return $rgb;
}

//--------------------------------------------------------------------------------------------------
//|	convert RGB color to hex
//--------------------------------------------------------------------------------------------------
//arg: r - red (0-255) [int]
//arg: g - green (0-255) [int]
//arg: b - blue (0-255) [int]
//returns: HTML hex color without leading # [string]

function imgRgbToHex($r, $g, $b) {	
    $r = dechex($r);
    $g = dechex($g);
    $b = dechex($b);

    $color = (strlen($r) < 2?'0':'').$r;
    $color .= (strlen($g) < 2?'0':'').$g;
    $color .= (strlen($b) < 2?'0':'').$b;
    return $color;
}

?>
