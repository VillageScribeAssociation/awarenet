<?

//--------------------------------------------------------------------------------------------------
//|	list banned users
//--------------------------------------------------------------------------------------------------
//TODO: paginate

function users_banned($args) {
	global $db, $user, $theme;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query the database
	//----------------------------------------------------------------------------------------------
	$conditions = array("role='banned'");
	$fields = 'UID, username, firstname, surname, editedOn, editedBy, alias';
	$range = $db->loadRange('users_user', $fields, $conditions, 'surname, firstname');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Name', 'User', 'Last Edit', 'By');

	foreach($range as $row) {
		$userUrl = '%%serverPath%%users/profile/' . $row['alias'];
		$userLink = "<a href='$userUrl'>" . $row['firstname'] . ' ' . $row['surname'] . "</a>\n";
		$editName = '[[:users::namelink::userUID=' . $row['editedBy'] . ':]]';
		$editLink = $theme->expandBlocks($editName, '');
		$table[] =  array($row['username'], $userLink, $row['editedOn'], $editName);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
