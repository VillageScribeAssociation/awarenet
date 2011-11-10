<?

	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');
	
//--------------------------------------------------------------------------------------------------
//|	template for displaying package details
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - overrrides UID if present [string]

function packages_showpackage($args) {
	global $user;
	global $theme;
	global $registry;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return '(package UID not given)'; }

	$package = new KPackage($args['UID']);
	if (false == $package->loaded) { return '(could not load package)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/showpackage.block.php');
	$labels = $package->extArray();

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
