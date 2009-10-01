<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//	blog stats (formatted for nav)
//--------------------------------------------------------------------------------------------------

function moblog_schoolstatsnav($args) {
	$sql = "select count(UID) as postCount, school from moblog group by school";
	$result = dbQuery($sql);
	$aryTable = array();
	$aryTable[] = array('School', 'Posts');

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$schoolUID = $row['school'];
		$schoolLink = expandBlocks('[[:schools::name::schoolUID='. $schoolUID .'::link=yes:]]', '');
		$aryTable[] = array($schoolLink, $row['postCount']);
	}

	$html = arrayToHtmlTable($aryTable, true, true);

	return $html;
}


?>