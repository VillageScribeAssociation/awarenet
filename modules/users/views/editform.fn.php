<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	form to edit a user record (admin, not for users to edit their own records)
//--------------------------------------------------------------------------------------------------
// * $args['raUID'] = recordAlias or UID of a user record

function users_editform($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$u = new Users($args['raUID']);
	return replaceLabels($u->extArray(), loadBlock('modules/users/views/editform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>