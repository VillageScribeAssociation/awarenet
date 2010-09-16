<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	summary of a module, with buttons to install/enable/disable a given module (perm:manage)
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this
//arg: modulename - name of a module [string]

function mods_summary($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and argument
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (array_key_exists('modulename', $args) == false) { return ''; }
	$module = new KModule($args['modulename']);	
	if (false == $module->loaded) { return 

	//----------------------------------------------------------------------------------------------
	//	construct the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/mods/views/summary.block.php');
	$html = $theme->replaceLabels($module->toArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
