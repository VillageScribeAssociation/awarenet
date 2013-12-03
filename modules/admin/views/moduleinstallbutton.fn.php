<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');

//--------------------------------------------------------------------------------------------------
//	button for re/installing modules
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a module [string]

function admin_moduleinstallbutton($args) {
	global $user, $theme;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('modulename', $args)) { return ''; }

	$model = new KModule($args['modulename']);
	if (false == $model->loaded) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/moduleinstallbutton.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

?>
