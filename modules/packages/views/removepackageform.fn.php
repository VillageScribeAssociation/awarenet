<?

//--------------------------------------------------------------------------------------------------
//|	shows a button to remove a package from automated updates
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - oveerrides UID if present [string]

function packages_removepackageform($args) {
	global $kapenta;
	global $theme;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return '(package UID not given)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/removepackageform.block.php');
	$html = $theme->replaceLabels($args, $block);
	return $html;	
}

?>
