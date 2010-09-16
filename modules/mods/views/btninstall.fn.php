<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	install button
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this
//arg: modulename - name of a module [string]

function mods_btninstall($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and argument
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('modulename', $args)) { return ''; }
	$module = new KModule($args['modulename'] . '');
	if ('yes' == $module->installed) { return '[installed]'; } 

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/mods/views/btninstall.block.php');
	$html = $theme->replaceLabels($module->toArray(), $block); }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
