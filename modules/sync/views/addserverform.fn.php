<?

//--------------------------------------------------------------------------------------------------
//|	form to add a new server (formatted for nav)
//--------------------------------------------------------------------------------------------------

function sync_addserverform($args) {
	global $theme, $user;
	if ('admin' != $user->role) { return ''; }
	$block = $theme->loadBlock('modules/sync/views/addserverform.block.php');
	return $block;
}



?>
