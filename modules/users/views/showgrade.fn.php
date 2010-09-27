<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - overrides raUID [string]
//arg: grade - grade which studenta are in [string]

function users_showgrade($args) {
	global $db;
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('schoolUID', $args)) { return ''; }
	if (false == array_key_exists('grade', $args)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load members from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "school='" . $db->addMarkup($args['schoolUID']) . "'";
	$conditions[] = "grade='" . $db->addMarkup($args['grade']) . "'";

	$range = $db->loadRange('Users_User', '*', $conditions, 'surname, firstname');

	//$sql = "select * from users "
	//	 . "where school='" . $db->addMarkup($args['schoolUID']) . "' "
	//	 . "and grade='" . $db->addMarkup($args['grade']) . "' "
	//	 . "order by surname, firstname";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) {
		$html .= "<div class='inlinequote'>No students have been added to this grade yet.</div>";

	} else {
		$html .= "<table noborder>\n";
		$odd = true;

		foreach ($range as $row) {
			$cell = "[[:users::summarynav::userUID=" . $row['UID'] . ":]]";
			if ($odd == true) { $html .= "\t<tr>\n\t\t<td valign='top'>$cell</td>\n"; }
			else { $html .= "\t\t<td valign='top'>$cell</td>\n\t</tr>\n"; }
			$odd = !$odd;
		}

		if (false == $odd) { $html .= "\t\t<td></td>\n\t</tr>\n"; }
		$html .= "</table>\n";
	}

	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
