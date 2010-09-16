<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums, grouped by school (noargs)
//--------------------------------------------------------------------------------------------------

function forums_summarylistall($args) {
	global $db, $user, $aliases;
	$html = '';		//% return value [string]

	//TODO: check permissions
	$sql = "select count(UID) as numForums, school from Forums_Board group by school";
	$result = $db->query($sql);

	if ($db->numRows($result) == 0) { return "(no forums as yet)<br/>\n"; }

	while ($row = $db->fetchAssoc($result)) { 
		$row = $db->rmArray($row);
		$schoolName = '';
		if ('' == $row['school']) { $schoolName = 'General'; }
		else { $schoolName = $aliases->getDefault('Schools_School', $row['school']);  }

		$html .= "[[:theme::navtitlebox::width=570::label=$schoolName:]]\n"
			   . "[[:forums::summarylist::school=" . $row['school'] . ":]]\n";

	}	

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
