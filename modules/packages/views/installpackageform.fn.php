<?

//--------------------------------------------------------------------------------------------------
//|	shows button for installing available package
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an available package [string]
//opt: packageUID - overrides UID if present [string]

function packages_installpackageform($args) {
	global $registry;
	global $user;
	global $theme;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] == $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/installpackageform.block.php');
	$labels = $args;
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
