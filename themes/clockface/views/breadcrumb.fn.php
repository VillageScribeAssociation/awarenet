<?

//--------------------------------------------------------------------------------------------------
//*	returns breadcrumb piece
//--------------------------------------------------------------------------------------------------
//arg: label - label/caption of this breadrumb part [string]
//arg: link - URL or javascript, default is none [string]

function theme_breadcrumb($args) {
	global $kapenta;
	global $utils;
	global $page;
	global $theme;

	$s = $theme->style;
	$s['label'] = 'label';
	$s['link'] = '';
	
	$html = '';					//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	read arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $s['label'] = $args['label']; }   
	if (true == array_key_exists('link', $args)) { $s['link'] = $args['link']; }
	if (strlen($s['label']) > 50) { $s['label'] = substr($s['label'], 0, 50) . '...'; }

	//----------------------------------------------------------------------------------------------
	//	construct fileName
	//----------------------------------------------------------------------------------------------
	//$fileName = 'themes/' . $kapenta->defaultTheme . '/drawcache/'
	//		  . 'bc_' . $s['pxxBreadcrumbHeight'] . '_'
	//		  . $utils->makeAlphaNumeric($s['label']) . '.png';

	//----------------------------------------------------------------------------------------------
	//	check if the image already exists
	//----------------------------------------------------------------------------------------------

	//if (false == file_exists($fileName)) {
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
	//$boxUrl = str_replace($installPath, $serverPath, $fileName);
	//$out = "<img src='" . $boxUrl . "' border='0' alt='" . $s['label'] . "'>";
	//$out = "<a href='" . $s['link'] . "'>" . $out . "</a>";
	$html = "<a href='" . $s['link'] . "' style='color: #4e4e4e;'><b>" . $s['label'] . "</b></a>";

	//------------------------------------------------------------------------------------------------------
	//	and we're done
	//------------------------------------------------------------------------------------------------------

	return $html;
}

?>
