<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	form for adding a model to a module definition (module.xml.php)
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//opt: modulename - alias of 'module' [string]

function admin_addmodelform($args) {
	global $kapenta;
	global $theme;

	$modulename = '';		//%	name of a kapenta module [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }	
	if (true == array_key_exists('module', $args)) { $modulename = $args['module']; }
	if (true == array_key_exists('modulename', $args)) { $modulename = $args['modulename']; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { return '(unknown module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/addmodelform.block.php');
	$labels = array('modulename' => $module->modulename);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
