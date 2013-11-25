<?

//--------------------------------------------------------------------------------------------------
//*	make an HTML form selection box listing templates available in this theme
//--------------------------------------------------------------------------------------------------
//TODO: this should really be a feature of the admin module

function theme_selecttemplates($args) {
	global $kapenta;
	$fieldName = 'template';
	$selected = '';
	$html = '';

	if (array_key_exists('selected', $args)) { $selected = $args['selected']; }
	if (array_key_exists('fieldName', $args)) { $selected = $args['fieldName']; }
	$temps = $kapenta->listTemplates($kapenta->defaultTheme);

	$html = "<select name='" . $fieldName . "'>\n";
	foreach($temps as $temp) {
		if ($temp != $selected) { $html .= "\t<option value='" . $temp . "'>$temp</option>"; } 
		else { $html .= "\t<option value='" . $temp . "' selected='selected'>$temp</option>"; }
	}
	$html .= "</select>\n";
	return $html;
}

?>
