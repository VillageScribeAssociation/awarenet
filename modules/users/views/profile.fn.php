<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	profile box on users profile page
//--------------------------------------------------------------------------------------------------
// * $args['userRA'] = recordAlias of record to summarise

function users_profile($args) {
	if (array_key_exists('userRa', $args) == false) { return false; }
	$u = new Users($args['userRa']);
	return replaceLabels($u->extArray(), loadBlock('modules/users/views/profile.block.php'));
}

//--------------------------------------------------------------------------------------------------

?>