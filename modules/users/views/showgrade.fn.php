<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all grades/years/forms/standards at a school
//--------------------------------------------------------------------------------------------------
//arg: schoolUID - overrides raUID [string]
//arg: grade - grade which studenta are in [string]

function users_showgrade($args) {
	global $kapenta;
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
	$conditions[] = "school='" . $kapenta->db->addMarkup($args['schoolUID']) . "'";
	$conditions[] = "grade='" . $kapenta->db->addMarkup($args['grade']) . "'";
	$conditions[] = "role != 'banned'";

	$range = $kapenta->db->loadRange('users_user', '*', $conditions, 'surname, firstname');

	//$sql = "select * from users "
	//	 . "where school='" . $kapenta->db->addMarkup($args['schoolUID']) . "' "
	//	 . "and grade='" . $kapenta->db->addMarkup($args['grade']) . "' "
	//	 . "order by surname, firstname";

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) {
		$html .= "<div class='inlinequote'>No students have been added to this grade yet.</div>";

	} else {
		$html .= "<table noborder>\n";
		$odd = true;

		foreach ($range as $item) {
			$cell = ''
			 . "<table noborder>"
			 . "<td width='50'>[[:users::avatar::size=thumbsm::userUID=" . $item['UID'] . ":]]</td>"
			 . "<td width='5px'></td>"
			 . "<td>[[:users::namelink::userUID=" . $item['UID'] . ":]]</td>"
			 . "</table>";

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
