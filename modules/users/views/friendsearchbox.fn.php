<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make iframe to search for friends
//--------------------------------------------------------------------------------------------------

function users_friendsearchbox($args) {
	global $user;

	if (false == $user->authHas('users', 'Users_User', 'view')) { return ''; }

	$html = "<iframe name='friendSearch' id='ifFSearch'
			 src='%%serverPath%%users/find/' 
			 width='300' height='200' 
			 frameborder='0'></iframe>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
