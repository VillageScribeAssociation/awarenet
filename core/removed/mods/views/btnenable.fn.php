<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	enable button (perm:manage)
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may use this
//arg: modulename - name of a module [string]

function mods_btnenable($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and argument
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('modulename', $args)) { return ''; }
	$module = new KModule($args['modulename'] . '');
	if (false == $module->loaded) { return ''; }
	if ('yes' == $module->enabled) { return '[enabled]'; }
	if ('no' == $module->installed) { return '[enable]'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/mods/views/btnenable.block.php');
	$html = $theme->replaceLabels($module->toArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
