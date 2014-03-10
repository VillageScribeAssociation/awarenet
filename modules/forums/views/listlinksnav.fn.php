<?

//--------------------------------------------------------------------------------------------------
//*	list all boards in the nav as links grouped by school displaying weight
//--------------------------------------------------------------------------------------------------
//TODO: opt to show with or without schools

function forums_listlinksnav($args) {
		global $kapenta;
		global $user;
		global $theme;

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO:

	//----------------------------------------------------------------------------------------------
	//	load list from database and sort by school
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('forums_board', 'UID, school, title, weight', '', 'weight ASC');
	
	if (0 == count($range)) { return $html . "(no forums yet)"; }

	$schools = array();
	foreach($range as $row) {
		if ('' == $row['school']) { $row['school'] = 'none'; }

		if (false == array_key_exists($row['school'], $schools)) { 
			$schoolName = 'General';
			if ('' != $row['school']) { 
				$block = "[[:schools::name::schoolUID=" . $row['school'] . ":]]";
				$schoolName = $theme->expandBlocks($block, '');
			}

			$schools[$row['school']] = array();
			$schools[$row['school']]['totalWeight'] = 0;
			$schools[$row['school']]['schoolName'] = $schoolName;
			$schools[$row['school']]['boards'] = array();
		}

		$schools[$row['school']]['boards'][] = $row;
		$schools[$row['school']]['totalWeight'] += $row['weight'];
	}

	$idx = array();	// TODO: remove this second loop
	foreach($schools as $sUID => $set) { $idx[$sUID] = $set['totalWeight']; }
	asort($idx);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($idx as $sUID => $totalWeight) {
		$html .= "<h3>" . $schools[$sUID]['schoolName'] . " (" . $schools[$sUID]['totalWeight'] . ")</h3>";
		foreach($schools[$sUID]['boards'] as $board) {
			$url = '%%serverPath%%forums/show/' . $board['UID'];
			$link = "<a href='" . $url . "'>" . $board['title'] . " (" . $board['weight'] . ")</a>";
			$html .= $link . "<br/>\n";
		}
		$html .= "<hr/>";
	}

	return $html;
}

?>
