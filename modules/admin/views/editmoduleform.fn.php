<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing module definitions
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//opt: xmodule - alias of 'module' [string]

function admin_editmoduleform($args) {
	global $theme;
	global $kapenta;

	$moduleName = '';					//%	name of a kapenta module [string]
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('module', $args)) { $moduleName = $args['module']; }
	if (true == array_key_exists('xmodule', $args)) { $moduleName = $args['xmodule']; }

	$module = new KModule($moduleName);
	if (false == $module->loaded) { return '(unknown module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/editmoduleform.block.php');
	$labels = $module->extArray();

	$labels['editmodelforms'] = '';
	foreach($module->models as $modelName => $modelAry) {
		$labels['editmodelforms'] .= ''
			. '[[:admin::editmodelform'
			 . '::modulename=' . $moduleName
			 . '::modelname=' . $modelName . ':]]';
	}

	if (0 == count($module->models)) { 
		$labels['editmodelforms'] = "<div class='inlinequote'>No defined models.</div>"; 
	}

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
