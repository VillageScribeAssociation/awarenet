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
	$sql = "select * from Users_User "
		 . "where (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(lastOnline)) < 7300 "
		 . "order by firstname";

	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "<a href='/users/profile/" . $row['alias'] . "'>"
			  . $row['firstname'] . ' ' . $row['surname'] . "</a> "
			  . "<small>(" . $row['grade'] . ") " . $row['timeDiff'] . "</small><br/>";	
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
