<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	login form
//--------------------------------------------------------------------------------------------------

function users_loginform($args) { 
	if ($user->data['ofGroup'] == 'admin') { return false; }
	if ($user->data['ofGroup'] == 'user') { return false; }
	return loadBlock('modules/users/views/loginform.block.php'); 
}

//--------------------------------------------------------------------------------------------------

?>