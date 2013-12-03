<?

//--------------------------------------------------------------------------------------------------
//|	create an HTML select element for choosing a kapenta module
//--------------------------------------------------------------------------------------------------
//arg: default - preselected item, name of a kapenta module [string]
//arg: varname - HTML form field name, default is 'module' [string]

function admin_selectmodule($args) {
	global $kapenta;

	$varname = 'module';				//%	HTML form field name [string]
	$default = '';						//%	preselected value [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and load list of modules
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }

	$mods = $kapenta->listModules();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='" . $varname . "'>\n";

	foreach($mods as $modName) {
		$selected = ($modName == $default) ? " selected='selected'" : '';
		$html .= "\t<option value='$modName'$selected>$modName</option>\n";
	}

	$html .= "</select>";

	return $html;
}

?>
