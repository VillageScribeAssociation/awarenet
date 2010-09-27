<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//	
//--------------------------------------------------------------------------------------------------
//arg: module - a kapenta module [string]
//arg: model - a type of object [string]
//opt: varname - form varibale name, default is 'permission' [string]

function admin_selectpermission($args) {
	global $kapenta, $user;
	$html = '';					//%	return value [string]
	$varname = 'permission';	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('module', $args)) { return '(no module)'; }
	if (false == array_key_exists('model', $args)) { return '(no module)'; }

	$module = new KModule($args['module']);
	if (false == $module->loaded) { return '(no such module)'; }
	if (false == array_key_exists($args['model'], $module->models)) { return '(no such model)'; }
	$model = $module->models[$args['model']];

	//TODO: error checking for HTML/js inclusion
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = "<select name='$varname'>\n";
	// add all permissions inherent to the model
	foreach($model->permissions as $permission) { $html = "  <option>$permission</option>\n"; }

	// add all permissions exported by other models
	$modules = $kapenta->listModules();
	foreach($modules as $moduleName) {
		$module = new KModule($moduleName);
		if (true == $module->loaded) {
			foreach($module->models as $model) {
				if ($model->name != $args['model']) {
					foreach($model->export as $expperm) { $html = "  <option>$expperm</option>\n"; }
				}
			}
		}
	}

	$html .= "</select>\n";
	return $html;
}

?>
