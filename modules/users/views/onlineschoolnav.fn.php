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

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return '[[:users::pleaselogin:]]'; }
	if (false == array_key_exists('school', $args)) { return '(school not specified)'; }

	//----------------------------------------------------------------------------------------------
	//	query database  //TODO: remove this join
	//----------------------------------------------------------------------------------------------
	$sql = "SELECT users_user.UID, firstname, surname, grade, alias FROM users_session, users_user "
		 . "WHERE users_user.school='" . $db->addMarkup($args['school']) . "' "
		 . "AND users_user.UID=users_session.createdBy "
		 . "AND users_session.status='active' "
		 . "ORDER BY firstname";

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	while($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "<a href='%%serverPath%%/users/profile/" . $row['alias'] . "'>"
			  . $row['firstname'] . ' ' . $row['surname'] . "</a> "
			  . "<small>(" . $row['grade'] . ")</small><br/>";	
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
