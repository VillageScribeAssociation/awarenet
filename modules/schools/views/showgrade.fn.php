<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - overrides raUID [string]
//arg: grade - grade which studenta are in [string]

function schools_showgrade($args) {
	global $db;

	global $serverPath;
	if (array_key_exists('schoolUID', $args) == false) { return false; }
	if (array_key_exists('grade', $args) == false) { return false; }
	$html = '';

	$sql = "select * from users "
		 . "where school='" . $db->addMarkup($args['schoolUID']) . "' "
		 . "and grade='" . $db->addMarkup($args['grade']) . "' "
		 . "order by surname, firstname";

	$result = $db->query($sql);

	if ($db->numRows($result) == 0) {
		$html .= "<div class='inlinequote'>No students have been added to this grade yet.</div>";

	} else {
		$html .= "<table noborder>\n";
		$odd = true;
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);

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