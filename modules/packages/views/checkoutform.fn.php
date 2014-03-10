<?

//--------------------------------------------------------------------------------------------------
//|	form to update all files in a package from the repository
//--------------------------------------------------------------------------------------------------
//arg: packageUID - UID of an installed package [string]

function packages_checkoutform($args) {
	global $kapenta;
 
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == array_key_exists('packageUID', $args)) { return '(package not specified)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $kapenta->theme->loadBlock('modules/packages/views/checkoutform.block.php');
	$html = $kapenta->theme->replaceLabels($args, $block);

	return $html;
}

?>
