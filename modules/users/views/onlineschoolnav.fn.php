<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	make a list of users who are currently online at a given school (formatted for nav)
//--------------------------------------------------------------------------------------------------
// * $args['school'] = UID of school 

function users_onlineschoolnav($args) {
	$html = '';
	if (array_key_exists('school', $args) == false) { return false; }

	$sql = "select * from users "
		 . "where (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(lastOnline)) < 7300 "
		 . "order by firstname";

	$result = dbQuery($sql);
	while($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$html .= "<a href='/users/profile/" . $row['recordAlias'] . "'>"
			  . $row['firstname'] . ' ' . $row['surname'] . "</a> "
			  . "<small>(" . $row['grade'] . ") " . $row['timeDiff'] . "</small><br/>";	
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>