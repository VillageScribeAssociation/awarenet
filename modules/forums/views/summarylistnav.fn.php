<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums at a specific school (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: school - UID of a school (not recordAlias) [string]

function forums_summarylistnav($args) {
	if (array_key_exists('school', $args) == false) { return false; }
	$html = '';

	$sql = "select * from forums "
		 . "where school='" . sqlMarkup($args['school']) . "' "
		 . "order by weight DESC";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) { 
			$row = sqlRMArray($row);
			$html .= "[[:forums::summarynav::forumUID=" . $row['UID'] . ":]]\n";
		}	

	} else { $html = "(no forums as yet)<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

