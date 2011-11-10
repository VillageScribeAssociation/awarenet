<?

//--------------------------------------------------------------------------------------------------
//|	form to add a new peer server (formatted for nav)
//--------------------------------------------------------------------------------------------------

function p2p_addpeerform($args) {
	global $theme, $user;
	if ('admin' != $user->role) { return ''; }
	$block = $theme->loadBlock('modules/p2p/views/addpeerform.block.php');
	return $block;
}



?>
