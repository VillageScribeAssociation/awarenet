<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------

// * $args['schoolUID'] = overrides raUID
// * $args['raUID'] = recordAlias or UID or schools entry

function schools_allgrades($args) {
	global $serverPath;
	if (array_key_exists('schoolUID', $args)) { $args['raUID'] = $args['schoolUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$html = '';

	$model = new School($args['raUID']);	
	$sql = "select grade, count(UID) as members from users "
		 . "where school='" . $model->data['UID'] . "' group by grade";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$link = $serverPath . 'schools/grade/grade_' . base64_encode($row['grade'])
			  . '/' . $model->data['recordAlias'];

		$html .= "<a href='" . $link . "'>" . $row['grade']
			  . " (". $row['members'] ." people)</a><br/>";
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>