<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a relationship to a a model definition in a module.xml.php file
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a kapenta module [string]
//arg: modelname - name of a model defined on this module [string]

function admin_addrelationshipform($args) {
	global $kapenta;
	global $theme;

	$modelName = '';	//%	name of a kapenta module [string]
	$moduleName = '';	//%	name of a model defined on this module [string]
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('modulename', $args)) { $moduleName = $args['modulename']; }
	if (true == array_key_exists('modelname', $args)) { $modelName = $args['modelname']; }
		
	$module = new KModule($moduleName);
	if (false == $module->loaded) { return '(unknown module)'; }

	if (false == array_key_exists($modelName, $module->models)) { return '(unkown model)'; }

	$model = new KModel();
	$model->loadArray($module->models[$modelName]);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/addrelationshipform.block.php');
	$labels = $model->extArray();
	$labels['modulename'] = $moduleName;
	$labels['modelname'] = $model->name;
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
