<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	blog stats (formatted for nav)
//--------------------------------------------------------------------------------------------------

function moblog_schoolstatsnav($args) {
	global $db, $user, $theme, $aliases;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	//TODO: add permissions and arguments

	//----------------------------------------------------------------------------------------------
	//	load states from database	//TODO: precache this
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('schools', 'Schools_School', 'show')) { return ''; }

	$sql = "SELECT count(UID) AS postCount, school "
		 . "FROM Moblog_Post "
		 . "GROUP BY school "
		 . "ORDER BY postCount DESC";

	//echo $sql;

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
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

			if ($schoolUID == $user->school) {
				$schoolLink = "<b>" . $schoolLink . "</b>";
				$row['postCount'] = "<b>" . $row['postCount'] . "</b>";
			}

			$aryTable[] = array($schoolLink, $row['postCount']);
		}
	}

	$html = $theme->arrayToHtmlTable($aryTable, true, true);

	return $html;
}


?>
