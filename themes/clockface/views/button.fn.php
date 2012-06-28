<?

	require_once($kapenta->installPath . 'themes/clockface/inc/mkmenuitem.inc.php');

//--------------------------------------------------------------------------------------------------
//	make a button graphic
//--------------------------------------------------------------------------------------------------
//arg: label - label/caption of the button [string]
//arg: alt - alt/tooltip text of the button [string]
//arg: selected - probably not used (yes|no) [string]

function theme_button($args) {
	global $kapenta, $page, $utils;

	$html = '';						//%	return value [string]
	$label = 'item';				//%	label/caption, default is 'item' [string]
	$alt = '';						//%	alt/tooltip text, default is none [string]
	$selected = '';					//%	probably not used (yes|no) [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $label = $args['label']; }
	if (true == array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (true == array_key_exists('selected', $args)) { $selected = $args['selected']; }

	//----------------------------------------------------------------------------------------------
	//	choose a filename
	//----------------------------------------------------------------------------------------------
	$fileName = 'button_' . $utils->makeAlphaNumeric($label) . '_' . $selected .  '.png';
	$fileName =  'themes/' . $kapenta->defaultTheme . '/images/drawcache/' . $fileName;

	//----------------------------------------------------------------------------------------------
	//	create the graphic if it does not exist
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->fileExists($fileName))
		{ theme__mkButtonImg($fileName, $label, $selected); }

	//------------------------------------------------------------------------------------------------------
	//	make and return HTML snippet
	//------------------------------------------------------------------------------------------------------
	$imgUrl = $kapenta->serverPath . $fileName;
	$html = "<img id='btn" . $kapenta->createUID() . "' class='menu1' "
		  . "src='" . $imgUrl . "' border='0' alt='" . $alt . "' />";

	return $html;
}

?>
