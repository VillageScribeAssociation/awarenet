<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a new user (to be displayed in the subnav, 300px wide, no arguments)
//--------------------------------------------------------------------------------------------------

function users_newuserform($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }
	return loadBlock('modules/users/views/newuserform.block.php');
}


//--------------------------------------------------------------------------------------------------

?>
