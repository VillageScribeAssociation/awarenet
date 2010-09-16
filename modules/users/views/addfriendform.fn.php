<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	nav form to add a user as a friend
//--------------------------------------------------------------------------------------------------
//arg: UID - recordAlias or UID or groups entry [string]
//opt: userUID - overrides raUID [string]

function users_addfriendform($args) {
	if (array_key_exists('userUID', $args) == true) { $args['UID'] = $args['userUID']; }
	if (array_key_exists('UID', $args) == false) { return false; }

	$model = new Users_Friendship();
	//TODO

	$html = '';
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
