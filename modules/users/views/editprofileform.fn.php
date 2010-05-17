<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a user record (admin, not for users to edit their own records)
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of a user record [string]

function users_editprofileform($args) {
	if (array_key_exists('raUID', $args) == false) { return false; }
	$u = new User($args['raUID']);
	$labels = $u->profile;
	$labels['UID'] = $u->data['UID'];
	return replaceLabels($labels, loadBlock('modules/users/views/editprofileform.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
