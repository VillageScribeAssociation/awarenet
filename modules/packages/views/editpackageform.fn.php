<?

//--------------------------------------------------------------------------------------------------
//|	form for editing package details (development feature)
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - overrides UID if present [string]

function packages_editpackageform($args) {
	global $user;
	global $theme;
	global $kapenta;	

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$package = new KPackage($args['UID']);
	if (false == $package->loaded) { return '(Could not load package: ' . $args['UID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/editpackageform.block.php');
	$labels = $package->extArray();
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
