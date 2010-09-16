<?

//--------------------------------------------------------------------------------------------------------------
//*	blocks api - defines actions the theme can take
//--------------------------------------------------------------------------------------------------------------

include 'navtitlebox.inc.php';
include 'breadcrumb.inc.php';

//--------------------------------------------------------------------------------------------------------------
//	make a form selection box listing templates available in this theme
//--------------------------------------------------------------------------------------------------------------

function theme_selecttemplates($args) {
	global $defaultTheme;
	$fieldName = 'template';
	$selected = '';
	$html = '';

	if (array_key_exists('selected', $args)) { $selected = $args['selected']; }
	if (array_key_exists('fieldName', $args)) { $selected = $args['fieldName']; }
	$temps = listTemplates($defaultTheme);

	$html = "<select name='" . $fieldName . "'>\n";
	foreach($temps as $temp) {
		if ($temp != $selected) { $html .= "\t<option value='" . $temp . "'>$temp</option>"; } 
		else { $html .= "\t<option value='" . $temp . "' selected='selected'>$temp</option>"; }
	}
	$html .= "</select>\n";
	return $html;
}

//--------------------------------------------------------------------------------------------------------------
//|	make a menu item
//--------------------------------------------------------------------------------------------------------------
//	arguments: [label][link][alt][selected]

function theme_menu($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $page;
	global $theme;
	$s = $theme->style;

	//------------------------------------------------------------------------------------------------------
	//	arguments
	//------------------------------------------------------------------------------------------------------
	$label = 'item'; $link = ''; $alt=''; $selected='no';
	if (array_key_exists('label', $args)) { $label = $args['label']; }
	if (array_key_exists('link', $args)) { $link = $args['link']; }
	if (array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (array_key_exists('selected', $args)) { $selected = $args['selected']; }

	//------------------------------------------------------------------------------------------------------
	//	choose a filename
	//------------------------------------------------------------------------------------------------------
	$fileName = 'menu_' . mkAlphaNumeric($label) . '_' . $selected .  '.png';
	$fileName = $installPath . 'themes/' . $defaultTheme . '/drawcache/' . $fileName;

	//------------------------------------------------------------------------------------------------------
	//	create the graphic if it does not exist
	//------------------------------------------------------------------------------------------------------
	if (file_exists($fileName) == false) { theme__mkMenuItem($s, $fileName, $label, $selected); }

	//------------------------------------------------------------------------------------------------------
	//	return html
	//------------------------------------------------------------------------------------------------------
	$imgUrl = str_replace($installPath, $serverPath, $fileName);
	$html = "<a href='" . $link . "'><img class='menu1' src='" . $imgUrl . "' border='0' /></a>";
	return $html;
}

function theme__mkMenuItem($s, $fileName, $label, $selected) {
	global $installPath;
	global $theme;
	$s = $theme->style;

	//------------------------------------------------------------------------------------------------------
	//	measure the text
	//------------------------------------------------------------------------------------------------------
	$fontFile = $installPath . 'data/fonts/' . $s['fonMenu1'];
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

	imagepng($img, $fileName);
}

//--------------------------------------------------------------------------------------------------------------
//	make a button graphic
//--------------------------------------------------------------------------------------------------------------
//	arguments: [label][alt][selected]

function theme_button($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $page;
	$s = $page->style;

	//------------------------------------------------------------------------------------------------------
	//	arguments
	//------------------------------------------------------------------------------------------------------
	$label = 'item'; $alt='';
	if (array_key_exists('label', $args)) { $label = $args['label']; }
	if (array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (array_key_exists('selected', $args)) { $selected = $args['selected']; }

	//------------------------------------------------------------------------------------------------------
	//	choose a filename
	//------------------------------------------------------------------------------------------------------
	$fileName = 'button_' . mkAlphaNumeric($label) . '_' . $selected .  '.png';
	$fileName = $installPath . 'themes/' . $defaultTheme . '/drawcache/' . $fileName;

	//------------------------------------------------------------------------------------------------------
	//	create the graphic if it does not exist
	//------------------------------------------------------------------------------------------------------
	if (file_exists($fileName) == false) { theme__mkButtonImg($s, $fileName, $label, $selected); }

	//------------------------------------------------------------------------------------------------------
	//	return html
	//------------------------------------------------------------------------------------------------------
	$imgUrl = str_replace($installPath, $serverPath, $fileName);
	$html = "<img id='btn" . createUID() . "' class='menu1' src='" . $imgUrl . "' border='0' alt='" . $alt . "' />";
	return $html;
}

function theme__mkButtonImg($s, $fileName, $label, $selected) {
	global $installPath;

	//------------------------------------------------------------------------------------------------------
	//	measure the text
	//------------------------------------------------------------------------------------------------------
	$fontFile = $installPath . 'data/fonts/' . $s['fonMenu1'];
	$bbox = imageftbbox($s['fnsMenu1'], 0, $fontFile, $label);
	$width = $bbox[4] - $bbox[6];

	//------------------------------------------------------------------------------------------------------
	//	make the graphic
	//------------------------------------------------------------------------------------------------------
	
	$width = $width + ($s['pxxButtonpad'] * 2);
	$height = $s['pxxButtonheight'];
	$bgRgb = imgHexToRgb($s['clrButtonbg']);
	$fgRgb = imgHexToRgb($s['clrButtonfg']);

	$img = imagecreatetruecolor($width, $height);
	$clrFg = imagecolorallocate($img, $fgRgb['r'], $fgRgb['g'], $fgRgb['b']);	
	$clrBg = imagecolorallocate($img, $bgRgb['r'], $bgRgb['g'], $bgRgb['b']);

	imagefilledrectangle($img, 0, 0, $width, $height, $clrBg);
	imagefttext($img, $s['fnsButton'], 0, $s['pxxButtonpad'], $s['pxxButtontop'], $clrFg, $fontFile, $label);

	imagepng($img, $fileName);
}

//--------------------------------------------------------------------------------------------------
//	make a submenu item
//--------------------------------------------------------------------------------------------------
//	arguments: [label][link][alt][selected]

function theme_submenu($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $theme;
	$s = $theme->style;

	//----------------------------------------------------------------------------------------------
	//	arguments
	//----------------------------------------------------------------------------------------------
	$label = 'item'; $link = ''; $alt=''; $selected='no';
	if (array_key_exists('label', $args)) { $label = $args['label']; }
	if (array_key_exists('link', $args)) { $link = $args['link']; }
	if (array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (array_key_exists('selected', $args)) { $selected = $args['selected']; }


	$html = "<span class='submenu'><a href='$link' >$label</a></span>";

	return $html;
}

//--------------------------------------------------------------------------------------------------
//	make tag cloud
//--------------------------------------------------------------------------------------------------
//	data is a base64_encoded, serialised array of [weight][link][label] triplets

function theme_tagcloud($args) {
	if (array_key_exists('data', $args) == false) { return '(no tag data)'; }
	global $serverPath;
	$html = '';

	$data = unserialize(base64_decode($args['data']));

	$maxWeight = 1;
	$minWeight = 0;

	foreach($data as $UID => $triple) {	// add min for negative values?
		if ($triple['weight'] > $maxWeight) { $maxWeight = $triple['weight']; }
	}

	foreach($data as $UID => $triple) {
		$size = floor((5 / $maxWeight) * $triple['weight']);
		$html .= "<a href='" . $serverPath . $triple['link'] . "' style='color: #444444;'>"
			 	. "<font size='" . $size . "'>" . $triple['label'] . "</font></a>\n";
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//	make pagination bar (page caterpillar: << | >> [1][2][3][4]...[15][16] )
//--------------------------------------------------------------------------------------------------
// * $args['page'] = page we're currently on
// * $args['total'] = total number of pages
// * $args['link'] = URL without page_x argument

function theme_pagination($args) {
	if (array_key_exists('page', $args) == false) { return 'error: page'; }
	if (array_key_exists('total', $args) == false) { return 'error: total'; }
	if (array_key_exists('link', $args) == false) { return 'error: link'; }
	$html = '';

	$prevLink = '';
	if ($args['page'] > 1) { 
		$prevLink = $args['link'] . 'page_' . ($args['page'] - 1);
		$prevLink = "<a href='" . $prevLink . "/' class='black'><< previous </a>" . ' | ';
	}

	$nextLink = '';
	if ($args['page'] < $args['total']) { 
		$nextLink = $args['link'] . 'page_' . ($args['page'] + 1);
		$nextLink = "<a href='" . $nextLink . "/' class='black'> next >> </a>"; 
	}

	$pagination = '';
	for ($i = 1; $i <= $args['total']; $i++) {
		if ($i == 1) {
			$pagination .= "<a href='" . $args['link'] . "' class='black'>[" . $i . "]</a> \n";
		} else {
			$link = $args['link'] . "page_" . $i;
			$pagination .= "<a href='" . $link . "/' class='black'>[" . $i . "]</a> \n";
		}
	}

	$html .= "<table noborder width='100%'><tr><td bgcolor='#dddddd'>\n&nbsp;&nbsp;";
	$html .= $prevLink . $nextLink . ' ' . $pagination . "<br/>\n";
	$html .= "</td></tr></table>\n";	

	return $html;
}

?>
