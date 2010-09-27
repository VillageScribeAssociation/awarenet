<?

//--------------------------------------------------------------------------------------------------
//	make a submenu item
//--------------------------------------------------------------------------------------------------
//arg: label - label/caption of menu item [string]
//arg: link - URL or Javascript [string]
//arg: alt - alt/tooltip text [string]
//arg: selected - not used at present (yes|no) [string]

function theme_submenu($args) {
	global $installPath;
	global $serverPath;
	global $defaultTheme;
	global $theme;

	$label = 'item';			//%	label/caption, default is 'item' [string]
	$link = '';			
	$alt = '';
	$selected = 'no';			//%	not used at present (yes|no) [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $label = $args['label']; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (true == array_key_exists('selected', $args)) { $selected = $args['selected']; }

	$html = "<span class='submenu'><a href='$link' >$label</a></span>";

	return $html;
}

?>
