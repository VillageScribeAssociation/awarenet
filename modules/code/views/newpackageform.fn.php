<?

	require_once($kapenta->installPath . 'modules/code/models/package.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the new package form (no args)
//--------------------------------------------------------------------------------------------------

function code_newpackageform($args) {
	global $kapenta;
	global $theme;
	$html = '';									//%	return value [string:html]

	if (false == $kapenta->user->authHas('code', 'code_package', 'new')) { return ''; }

	$html = $theme->loadBlock('modules/code/views/newpackageform.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
