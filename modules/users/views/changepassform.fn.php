<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to change password
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]

function users_changepassform($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$u = new Users_User($args['raUID']);
	return $theme->replaceLabels($u->extArray(), $theme->loadBlock('modules/users/views/changepassform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>