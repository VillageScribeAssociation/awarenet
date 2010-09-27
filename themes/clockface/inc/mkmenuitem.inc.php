<?

//--------------------------------------------------------------------------------------------------
//|	utility function for making menu items and buttons
//--------------------------------------------------------------------------------------------------
//arg: fileName - of new image file, relative to installPath [string]
//arg: label - caption of the menu item [string]
//arg: selected - is this item selected (yes|no) [string]

function theme__mkMenuItem($fileName, $label, $selected) {
	global $kapenta;
	global $theme;
	$s = $theme->style;

	//------------------------------------------------------------------------------------------------------
	//	measure the text
	//------------------------------------------------------------------------------------------------------
	$fontFile = $kapenta->installPath . 'data/fonts/' . $s['fonMenu1'];
	$bbox = imageftbbox($s['fnsMenu1'], 0, $fontFile, $label);
	$width = $bbox[4] - $bbox[6];

	//------------------------------------------------------------------------------------------------------
	//	make the graphic
	//------------------------------------------------------------------------------------------------------
	
	$width = $width + ($s['pxxMenu1pad'] * 2);
	$height = $s['pxxMenu1height'];
	$bgRgb = imgHexToRgb($s['clrMenu1bg']);
	$fgRgb = imgHexToRgb($s['clrMenu1fg']);

	$img = imagecreatetruecolor($width, $height);
	$clrFg = imagecolorallocate($img, $fgRgb['r'], $fgRgb['g'], $fgRgb['b']);	
	$clrBg = imagecolorallocate($img, $bgRgb['r'], $bgRgb['g'], $bgRgb['b']);

	imagefilledrectangle($img, 0, 0, $width, $height, $clrBg);
	imagefttext($img, $s['fnsMenu1'], 0, $s['pxxMenu1pad'], $s['pxxMenu1top'], $clrFg, $fontFile, $label);

	imagepng($img, $kapenta->installPath . $fileName);
}

?>
