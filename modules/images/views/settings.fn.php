<?

	require_once($kapenta->installPath . 'modules/images/models/transforms.set.php');

//--------------------------------------------------------------------------------------------------
//|	makes form for changing image module registry settings
//--------------------------------------------------------------------------------------------------

function images_settings($args) {
	global $kapenta;
	global $theme;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	$dimensions = new Images_Transforms();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/images/views/settings.block.php');

	$labels = array(
		'dimensionsTable' => $dimensions->toHtml(),
		'addPresetForm' => $theme->loadBlock('modules/images/views/addpresetform.block.php'),
		'setUnavailableForm' => $theme->loadBlock('modules/images/views/setunavailable.block.php')
	);

	$html = $theme->replaceLabels($labels, $block); 

	return $html;
}

?>
