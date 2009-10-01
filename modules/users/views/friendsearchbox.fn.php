<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	make iframe to search for friends
//--------------------------------------------------------------------------------------------------

function users_friendsearchbox($args) {
	if (authHas('users', 'view', '') == false) { return false; }
	$html = "<iframe name='friendSearch' id='ifFSearch'
			 src='%%serverPath%%users/find/' width='300' height='200' frameborder='0'></iframe>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>