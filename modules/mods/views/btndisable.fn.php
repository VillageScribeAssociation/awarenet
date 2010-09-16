<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//|	enable button
//--------------------------------------------------------------------------------------------------
//role: admin - only administartors may use this
//arg: modulename - name of a module [string]

function mods_btndisable($args) {
	global $theme, $user;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and argument
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('modulename', $args)) { return ''; }
	$module = new KModule($args['modulename'] . '');
	if (false == $module->loaded) { return ''; }
	if ('no' == $module->enabled) { return '[disabled]'; } 

	$block = $theme->loadBlock('modules/mods/views/btndisable.block.php');
	$html = $theme->replaceLabels($model->toArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
