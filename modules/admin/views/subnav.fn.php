<?


//--------------------------------------------------------------------------------------------------
//|	nav bar (perm:administer)
//--------------------------------------------------------------------------------------------------

function admin_subnav($args) {
	global $theme;

	global $user;
	if ('admin' != $user->role) { return ''; }
	return $theme->replaceLabels(array(), $theme->loadBlock('modules/admin/views/subnav.block.php'));
}


?>
