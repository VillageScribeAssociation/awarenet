<?

//--------------------------------------------------------------------------------------------------
//|	form for adding software sources to kapenta
//--------------------------------------------------------------------------------------------------

function packages_addsourceform($args) {
	global $user;
	global $theme;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and load the block
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	$html = $theme->loadBlock('modules/packages/views/addsourceform.block.php');

	return $html;
}

?>
