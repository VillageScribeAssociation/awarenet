<?

//--------------------------------------------------------------------------------------------------
//	returns a navbox title graphic
//--------------------------------------------------------------------------------------------------
//	arguments:
//		link:	URL - what happens when you click on this - default=none
//		label:	text displayed on the box

function theme_breadcrumb($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $page;
	global $theme;
	$s = $theme->style;
	
	//----------------------------------------------------------------------------------------------
	//	read arguments
	//----------------------------------------------------------------------------------------------
	$s['label'] = 'label'; $s['link'] = '';

	if (array_key_exists('label', $args)) 	{ $s['label'] = $args['label']; }   
	if (array_key_exists('link', $args)) 	{ $s['link'] = $args['link']; }

	if (strlen($s['label']) > 50) { $s['label'] = substr($s['label'], 0, 50) . '...'; }

	//----------------------------------------------------------------------------------------------
	//	construct fileName
	//----------------------------------------------------------------------------------------------

	$fileName = $installPath . 'themes/' . $defaultTheme . '/drawcache/'
			  . 'bc_' . $s['pxxBreadcrumbHeight'] . '_' . mkAlphaNumeric($s['label']) . '.png';

	//----------------------------------------------------------------------------------------------
	//	check if the image already exists
	//----------------------------------------------------------------------------------------------

	//if (file_exists($fileName) == false) {
		//------------------------------------------------------------------------------------------
		//	img file does not exist, make it
		//------------------------------------------------------------------------------------------
		//$height = $s['pxxBreadcrumbHeight'];
		//$fontFile = $installPath . 'data/fonts/' . $s['fonBreadcrumb'];
		//$fontSize = $s['fnsBreadcrumb'];
		//$fontX = $s['pxxBreadcrumbFonX'];
		//$fontY = $s['pxxBreadcrumbFonY'];

		//$bbox = imageftbbox($fontSize, 0, $fontFile, $s['label']);		// measure size of text
		//$width = ($bbox[4] - $bbox[7]) - 25;

		//------------------------------------------------------------------------------------------
		//	create blank image
		//------------------------------------------------------------------------------------------
		//$img = imagecreatetruecolor($width, $height);
		//$bgRgb = imgHexToRgb($s['clrBreadcrumbBg']);	
		//$fgRgb = imgHexToRgb($s['clrBreadcrumbFg']);
		//$clrBg = imagecolorallocate($img, $bgRgb['r'], $bgRgb['g'], $bgRgb['b']);
		//$clrFg = imagecolorallocate($img, $fgRgb['r'], $fgRgb['g'], $fgRgb['b']);

		//imagefilledrectangle($img, 0, 0, $width, $height, $clrBg);

		//imagefttext($img, $fontSize, 0, $fontX, $fontY, $clrFg, $fontFile, $s['label']);
		//imagepng($img, $fileName);
	//}

	//------------------------------------------------------------------------------------------------------
	//	make html snippet 
	//------------------------------------------------------------------------------------------------------

	$boxUrl = str_replace($installPath, $serverPath, $fileName);

	//$out = "<img src='" . $boxUrl . "' border='0' alt='" . $s['label'] . "'>";
	//$out = "<a href='" . $s['link'] . "'>" . $out . "</a>";
	$out = "<a href='" . $s['link'] . "' style='color: #4e4e4e;'><b>" . $s['label'] . "</b></a>";

	//------------------------------------------------------------------------------------------------------
	//	and we're done
	//------------------------------------------------------------------------------------------------------

	return $out;
}

?>
