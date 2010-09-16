<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a user record (admin, not for users to edit their own records)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]

function users_editform($args) {
	global $theme;

	if (array_key_exists('raUID', $args) == false) { return false; }
	$u = new Users_User($args['raUID']);
	return $theme->replaceLabels($u->extArray(), $theme->loadBlock('modules/users/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>