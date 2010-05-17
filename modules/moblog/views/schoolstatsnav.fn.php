<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	blog stats (formatted for nav)
//--------------------------------------------------------------------------------------------------

function moblog_schoolstatsnav($args) {
	//TODO: use dbLoadRange
	$sql = "select count(UID) as postCount, school from moblog group by school";
	$result = dbQuery($sql);
	$aryTable = array();
	$aryTable[] = array('School', 'Posts');

	while ($row = dbFetchAssoc($result)) {
		//TODO: remove inermodule dependancy (block on schools module?)
		$row = sqlRMArray($row);
		$schoolUID = $row['school'];
		if (dbRecordExists('schools', $schoolUID) == true) {
			$schoolRa = raGetDefault('schools', $schoolUID);
			$nameBlock = '[[:schools::name::schoolUID='. $schoolUID .'::link=no:]]';
			$schoolName = expandBlocks($nameBlock, '');
			$schoolUrl = "%%serverPath%%moblog/school/" . $schoolRa;
			$schoolLink = "<a href='" . $schoolUrl . "'>$schoolName</a>";
			$aryTable[] = array($schoolLink, $row['postCount']);
		}
	}

	$html = arrayToHtmlTable($aryTable, true, true);

	return $html;
}


?>
