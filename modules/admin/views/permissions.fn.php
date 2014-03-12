<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	show/edit permissions belonging to a given module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapent module [string]

function admin_permissions($args) {
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == array_key_exists('module', $args)) { return '(module not specified)'; }

	$module = new KModule($args['module']);
	if (false == $module->loaded) { return '(module not found)'; }
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($module->models as $model) {
		$modelname = $model['name'];
		$html .= ''
		 . '<h2>' . $model['name'] . '</h2>'
		 . '[[:users::permissions::'
		 . 'module='. $module->modulename .'::model='. $modelname .':]]'
		 . '[[:admin::grantpermissionform::'
		 . 'module='. $module->modulename .'::model='. $modelname .':]]';
	}

	return $html;
}

?>
