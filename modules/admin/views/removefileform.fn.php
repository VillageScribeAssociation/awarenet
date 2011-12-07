<?

//--------------------------------------------------------------------------------------------------
//|	delete a file from this kapenta installation
//--------------------------------------------------------------------------------------------------
//arg: fileName - location relative to installPath [string]
//arg: return - location relative to serverPath [string]

function admin_removefileform($args) {
	global $user;
	global $theme;
	$html = '';
	
	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('fileName', $args)) { return '(fileName not given)'; }
	if (false == array_key_exists('return', $args)) { return '(return value not given)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/removefileform.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}


?>
