<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//|	makes an HTML select element to choose a module permission
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of object [string]
//opt: varname - html form element name, default is role [string]

function admin_selectpermission($args) {
	global $kapenta;
	global $kapenta;

	$default = '';					//%	reserved [string]
	$varname = 'permission';		//%	html field name [string]
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
	//	add all permissions native to this object
	//----------------------------------------------------------------------------------------------
	$html .= "<select name='$varname'>\n";

	foreach($model['permissions'] as $permission) {
		$selected = '';
		if ($permission == $default) { $selected = " selected='selected'"; }
		$html .= "\t<option value='$permission'$selected>$permission</option>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	get all permissions exported by some other object
	//----------------------------------------------------------------------------------------------
	$modules = $kapenta->listModules();
	foreach($modules as $modName) {
		$tempModule = new KModule($modName);
		foreach($tempModule->models as $tempModel) {
			foreach($tempModel['export'] as $export) {
				$selected = '';				
				$html .= "\t<option value='$export'$selected>$export</option>\n";
			}
		}
	}

	$html .= "</select>\n";

	return $html;
}

?>
