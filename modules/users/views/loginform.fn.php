<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	site login form
//--------------------------------------------------------------------------------------------------

function users_loginform($args) { 
	if ($user->data['ofGroup'] == 'admin') { return false; }
	if ($user->data['ofGroup'] == 'user') { return false; }
	return loadBlock('modules/users/views/loginform.block.php'); 
}

//--------------------------------------------------------------------------------------------------

?>
