<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	makes an HTML select element to choose a relationship between an object and a user
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of object [string]
//opt: varname - html form element name, default is role [string]

function admin_selectrelationship($args) {
	global $kapenta;

	$default = '';					//%	reserved [string]
	$varname = 'relationship';		//%	html field name [string]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $args)) { return '(module not specified)'; }
	if (false == array_key_exists('model', $args)) { return '(model not specified)'; }
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }

	$module = new KModule($args['module']);
	if (false == $module->loaded) { return '(module not found)'; }
	if (false == $module->hasModel($args['model'])) { return '(model not found)'; }

	$model = $module->models[$args['model']];

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='$varname'>\n";
	$html .= "\t<option value=''></option>\n";

	foreach($model['relationships'] as $relationship) {
		$selected = '';
		if ($relationship == $default) { $selected = " selected='selected'"; }
		$html .= "\t<option value='$relationship'$selected>$relationship</option>\n";
	}

	$html .= "</select>\n";

	return $html;
}

?>
