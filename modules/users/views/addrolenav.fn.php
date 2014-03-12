<?

	require_once($kapenta->installPath . 'modules/users/models/role.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new Role object, formatted for nav
//--------------------------------------------------------------------------------------------------

function users_addrolenav($args) {
		global $kapenta;
		global $theme;


	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('users', 'users_role', 'new')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/users/views/addrolenav.block.php');

	return $html;
}

?>
