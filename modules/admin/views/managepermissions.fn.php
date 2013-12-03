<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	displays a form to edit the permissions of a single module
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]

function admin_managepermissions($args) {
	global $kapenta;
	global $user;
	global $theme;
	global $session;

	$moduleName = '';			//%	name of kapenta module [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return $html; }
	if (true == array_key_exists('module', $args)) { $moduleName = $args['module']; }
	if (false == $kapenta->moduleExists($moduleName)) { return '(module not recognized)'; }

	$module = new KModule($moduleName);
	if (false == $module->loaded) { 
		$session->msgAdmin('Could not load module: ' . $moduleName, 'bad');
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/managepermissions.block.php');
	$labels = array(
		'moduleName' => $module->modulename
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
