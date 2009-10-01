<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	nav form to add a user as a friend
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = overrides raUID
// * $args['UID'] = recordAlias or UID or groups entry

function users_addfriendform($args) {
	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }

	$model = new Friendship();
	//TODO

	$html = '';
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>