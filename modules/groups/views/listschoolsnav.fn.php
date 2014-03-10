<?

//--------------------------------------------------------------------------------------------------
//|	makes a lit of schools at which a group is active, formatted for the nav
//--------------------------------------------------------------------------------------------------
//arg: groupUID - UID of a Groups_Group object [string]

function groups_listschoolsnav($args) {
	global $kapenta;
	$html = '';							//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('groupUID', $args)) { return '(group UID not given)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("groupUID='" . $kapenta->db->addMarkup($args['groupUID']) . "'");
	$range = $kapenta->db->loadRange('groups_schoolindex', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { $html .= "<div class='inlinequote'>None</div>"; }

	foreach ($range as $item) {
		$html .= "[[:schools::summarynav::schoolUID=" . $item['schoolUID'] . ":]]\n";
	}

	return $html;
}

?>
