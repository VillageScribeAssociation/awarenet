<?


//--------------------------------------------------------------------------------------------------
//|	admin menu (perm:administer)
//--------------------------------------------------------------------------------------------------

function admin_menu($args) {
		global $kapenta;
		global $theme;

	if ('admin' != $kapenta->user->role) { return ''; }
	$block = $theme->loadBlock('modules/admin/views/menu.block.php');
	return $block;
}

//--------------------------------------------------------------------------------------------------

?>
