<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums, grouped by school (noargs)
//--------------------------------------------------------------------------------------------------

function forums_summarylistall($args) {
	$html = '';

	$sql = "select count(UID) as numForums, school from forums group by school";
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) { 
			$row = sqlRMArray($row);
			$schoolName = '';
			if ($row['school'] == '') {
				$schoolName = 'General';
			} else {
				$schoolName = raGetDefault('schools', $row['school']);
			}
			$html .= "[[:theme::navtitlebox::width=570::label=$schoolName:]]\n"
				   . "[[:forums::summarylist::school=" . $row['school'] . ":]]\n";
		}	
	} else { $html = "(no forums as yet)<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
