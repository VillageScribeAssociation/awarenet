<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make a list of users who are currently online at a given school (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: school - UID of school [string]

function users_onlineschoolnav($args) {
	global $db;

	global $user;
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }

	$html = '';

	if (array_key_exists('school', $args) == false) { return false; }
	// TODO this needs fixing
	$sql = "select users_user.UID, firstname, surname, grade, alias from users_login, users_user "
		 . "where users_user.school='" . $db->addMarkup($args['school']) . "' "
		 . "and users_user.UID=users_login.userUID "
		 . "order by firstname";

	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "<a href='/users/profile/" . $row['alias'] . "'>"
			  . $row['firstname'] . ' ' . $row['surname'] . "</a> "
			  . "<small>(" . $row['grade'] . ")</small><br/>";	
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
