<?

	require_once($installPath . 'modules/projects/models/membership.mod.php');
	require_once($installPath . 'modules/projects/models/projectrevision.mod.php');
	require_once($installPath . 'modules/projects/models/projects.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all projects which a user belongs to (formatted for nav)
//--------------------------------------------------------------------------------------------------
// * $args['userUID'] = UID of a user

function projects_listuserprojectsnav($args) {
	if (array_key_exists('userUID', $args) == false) { return false; }
	$html = '';
	$sql = "select * from projectmembers "
		 . "where userUID='" . sqlMarkup($args['userUID']) . "' and role != 'asked' "
		 . "order by joined";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:projects::summarynav::projectUID=" . $row['projectUID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>