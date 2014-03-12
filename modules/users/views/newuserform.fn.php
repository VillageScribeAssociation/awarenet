<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new user (to be displayed in the subnav, 300px wide, no arguments)
//--------------------------------------------------------------------------------------------------

function users_newuserform($args) {
	global $theme;

	global $kapenta;
	if ('admin' != $kapenta->user->role) { return false; }
	return $theme->loadBlock('modules/users/views/newuserform.block.php');
}


//--------------------------------------------------------------------------------------------------

?>