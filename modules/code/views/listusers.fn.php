<?

//--------------------------------------------------------------------------------------------------
//	list all project comitters
//--------------------------------------------------------------------------------------------------

function code_listusers($args) {
	global $db;

	$sql = "select * from codeprojects order by title"; // for all projects
	$result = $db->query($sql);
	$html = '';

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$link = '%%serverPath%%code/project/' . $row['recordAlias'];
		$html .= "<h2><a href='" . $link . "'>" . $row['title'] . "</a></h2>\n"
			   . "[[:code::listprojectusers::projectUID=" . $row['UID'] . ":]]\n"
			   . "<hr/>\n"; 
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>