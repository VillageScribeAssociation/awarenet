<?

//--------------------------------------------------------------------------------------------------
//	display n most active projects (by number of revisions)
//--------------------------------------------------------------------------------------------------
//opt: num - number of projects to display, default is 10 (int) [string]

function projects_mostactivenav($args) {
	global $db, $theme, $user;
	$html = '';		//%	return value [string]
	$num = 10;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if ($num < 1) { $num = 1; }

	//----------------------------------------------------------------------------------------------
	//	count revisions
	//----------------------------------------------------------------------------------------------

	$sql = "SELECT projectUID, count(UID) as numRevisions "
		 . "FROM projects_revision "
		 . "WHERE projectUID != ''"
		 . "GROUP BY projectUID "
		 . "ORDER BY numRevisions DESC LIMIT $num";

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$html .= "[[:projects::summarynav::projectUID=" . $row['projectUID'] . ":]]";
	}
	
	return $html;
}

?>
