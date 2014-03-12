<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	form for granting users permissions on kapenta objects
//--------------------------------------------------------------------------------------------------
//arg: module - module in question [string]

function admin_grantpermissionform($args) {
	global $kapenta;
	global $theme;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	$module = new KModule($args['module']);
	if (false == $module->loaded) { return '(could not load module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/grantpermissionform.block.php');	
	$labels = array('module' => $module->modulename, 'model' => $args['model']);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
