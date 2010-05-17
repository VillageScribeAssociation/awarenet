<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all groups which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: userUID - UID of a user [string]

function groups_listusergroupsnav($args) {
	if (array_key_exists('userUID', $args) == false) { return false; }
	$html = '';
	$sql = "select * from groupmembers "
		 . "where userUID='" . sqlMarkup($args['userUID']) . "' order by admin='yes'";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:groups::summarynav::groupUID=" . $row['groupUID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

