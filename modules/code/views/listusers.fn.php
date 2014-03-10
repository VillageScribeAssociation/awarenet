<?

//--------------------------------------------------------------------------------------------------
//	list all project comitters
//--------------------------------------------------------------------------------------------------

function code_listusers($args) {
	global $kapenta;

	$sql = "select * from codeprojects order by title"; // for all projects
	$result = $kapenta->db->query($sql);
	$html = '';

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$row = $kapenta->db->rmArray($row);
		$link = '%%serverPath%%code/project/' . $row['recordAlias'];
		$html .= "<h2><a href='" . $link . "'>" . $row['title'] . "</a></h2>\n"
			   . "[[:code::listprojectusers::projectUID=" . $row['UID'] . ":]]\n"
			   . "<hr/>\n"; 
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>