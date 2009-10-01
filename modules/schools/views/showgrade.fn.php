<?

	require_once($installPath . 'modules/schools/models/schools.mod.php');

//--------------------------------------------------------------------------------------------------
//	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------
// * $args['schoolUID'] = overrides raUID
// * $args['grade'] = grade which studenta are in

function schools_showgrade($args) {
	global $serverPath;
	if (array_key_exists('schoolUID', $args) == false) { return false; }
	if (array_key_exists('grade', $args) == false) { return false; }
	$html = '';

	$sql = "select * from users "
		 . "where school='" . sqlMarkup($args['schoolUID']) . "' "
		 . "and grade='" . sqlMarkup($args['grade']) . "' "
		 . "order by surname, firstname";

	$result = dbQuery($sql);

	if (dbNumRows($result) == 0) {
		$html .= "<div class='inlinequote'>No students have been added to this grade yet.</div>";

	} else {
		$html .= "<table noborder>\n";
		$odd = true;
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);

			$cell = "[[:users::summarynav::userUID=" . $row['UID'] . ":]]";

			if ($odd == true) {
				$html .= "\t<tr>\n";
				$html .= "\t\t<td>$cell</td>\n";
				$odd = false;
			} else {
				$html .= "\t\t<td>$cell</td>\n";
				$html .= "\t</tr>\n";
				$odd = true;
			}
		}

		if ($odd == false) {
				$html .= "\t\t<td></td>\n";
				$html .= "\t</tr>\n";
		}
		$html .= "</table>\n";
	}

	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>