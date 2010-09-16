<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	blog stats (formatted for nav)
//--------------------------------------------------------------------------------------------------

function moblog_schoolstatsnav($args) {
	global $db, $user, $theme, $aliases;
	if (false == $user->authHas('schools', 'Schools_School', 'show')) { return ''; }

	$sql = "select count(UID) as postCount, school from Moblog_Post group by school";
	$result = $db->query($sql);
	$aryTable = array();
	$aryTable[] = array('School', 'Posts');

	while ($row = $db->fetchAssoc($result)) {
		//TODO: remove inermodule dependancy (block on schools module?)
		$row = $db->rmArray($row);
		$schoolUID = $row['school'];
		if (true == $db->objectExists('Schools_School', $schoolUID)) {
			$schoolRa = $aliases->getDefault('Schools_School', $schoolUID);
			$nameBlock = '[[:schools::name::schoolUID='. $schoolUID .'::link=no:]]';
			$schoolName = $theme->expandBlocks($nameBlock, '');
			$schoolUrl = "%%serverPath%%moblog/school/" . $schoolRa;
			$schoolLink = "<a href='" . $schoolUrl . "'>$schoolName</a>";
			$aryTable[] = array($schoolLink, $row['postCount']);
		}
	}

	$html = $theme->arrayToHtmlTable($aryTable, true, true);

	return $html;
}


?>
