<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make iframe to search for friends
//--------------------------------------------------------------------------------------------------

function users_usersearchbox($args) {
	global $user, $theme;
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_user', 'view')) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = $theme->loadBlock('modules/users/views/usersearchbox.block.php');

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
