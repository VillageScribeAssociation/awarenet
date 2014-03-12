<?

	require_once($kapenta->installPath . 'modules/users/models/preset.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a theme preset
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID or Alias of a Users_Preset object [string]
//arg: presetUID - overrides raUID if present [string]

function users_showpreset($args) {
	global $theme;
	global $kapenta;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('presetUID', $args)) { $args['raUID'] = $args['presetUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(preset not specified)'; }

	$model = new Users_Preset($args['raUID']);
	if (false == $model->loaded) { return '(preset not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$labels = $model->extArray();
	$block = $theme->loadBlock('modules/users/views/showpreset.block.php');
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
