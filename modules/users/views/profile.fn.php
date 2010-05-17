<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	profile box on users profile page
//--------------------------------------------------------------------------------------------------
//arg: userRA - recordAlias of record to summarise [string]

function users_profile($args) {
	if (array_key_exists('userRa', $args) == false) { return false; }
	$u = new User($args['userRa']);
	return replaceLabels($u->extArray(), loadBlock('modules/users/views/profile.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>
