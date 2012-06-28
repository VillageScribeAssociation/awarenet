<?

	require_once($kapenta->installPath . 'themes/clockface/inc/mkmenuitem.inc.php');

//--------------------------------------------------------------------------------------------------
//|	make a menu item
//--------------------------------------------------------------------------------------------------
//arg: label - label or caption of this menu item [string]
//arg: link - URL or Javascript for when this item is selected [string]
//arg: alt - alt/tooltop text of menu item [string]
//arg: selected - if this is the section of the site currently displayed (yes|no) [string]
//TODO: Stop making images for this, use HTML+CSS, srsly

function theme_menu($args) {
	global $kapenta;
	global $page;
	global $theme;
	global $utils;

	$label = 'item';			//%	default label [string]
	$link = '';					//%	no default link [string]
	$alt = '';					//% no default alt/tooltip text [string]
	$selected = 'no';			//% item is not 'selected' by default [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('label', $args)) { $label = $args['label']; }
	if (true == array_key_exists('link', $args)) { $link = $args['link']; }
	if (true == array_key_exists('alt', $args)) { $alt = $args['alt']; }
	if (true == array_key_exists('selected', $args)) { $selected = $args['selected']; }

	if ('' != $alt) { $alt = "alt='" . htmlentities($alt) . "' "; }

	//----------------------------------------------------------------------------------------------
	//	return html
	//----------------------------------------------------------------------------------------------
	$html = "<a href='" . $link . "' $alt class='menu'>$label</a>";

	return $html;
}

?>
