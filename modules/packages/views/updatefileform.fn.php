<?

//--------------------------------------------------------------------------------------------------
//|	form to update a single file from repository
//--------------------------------------------------------------------------------------------------
//arg: packageUID - UID of an installed package [string]
//arg: fileUID - UID of a file inthis package [string]

function packages_updatefileform($args) {
	global $theme;
	global $user;

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('packageUID', $args)) { return '(packageUID not given)'; }
	if (false == array_key_exists('fileUID', $args)) { return '(fileUID not given)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/updatefileform.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}


?>
