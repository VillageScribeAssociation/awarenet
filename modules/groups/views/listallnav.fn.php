<?

	require_once($installPath . 'modules/groups/models/group.mod.php');
	require_once($installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all formatted for navigation bar
//--------------------------------------------------------------------------------------------------
//opt: school - UID of a school, constrains results [string]
//opt: sameschool - UID of a group, constrains results to those of the same school [string]

function groups_listallnav($args) {
	$school = ''; $userUID = ''; $sameschool = '';
	if (array_key_exists('school', $args)) { $school = $args['school']; }
	if (array_key_exists('sameschool', $args)) { $sameschool = $args['sameschool']; }

	$sql = "select * from groups order by name";

	if ($school != '') {
		$schoolUID = raGetOwner($school, 'schools');
		$sql = "select * from groups where school='" . $schoolUID . "' order by name";
	}

	if ($sameschool != '') {
		$tG = new Group($sameschool);
		$sql = "select * from groups where school='" . $tG->data['school'] . "' order by name";
	}

	$result = dbQuery($sql);
	$html = '';
	while ($row = dbFetchAssoc($result)) {
		$html .= "[[:groups::summarynav::groupUID=" . $row['UID'] . ":]]";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

