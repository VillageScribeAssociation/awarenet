<?


//--------------------------------------------------------------------------------------------------
//	nav bar (perm:administer)
//--------------------------------------------------------------------------------------------------

function admin_subnav($args) {
	if (authHas('admin', 'administer', '')) {
		return replaceLabels(array(), loadBlock('modules/admin/views/subnav.block.php'));
	}
}


?>