<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums, grouped by school (noargs)
//--------------------------------------------------------------------------------------------------
//opt: num - max number of threads to show per board (int) [string]

function forums_summarylistall($args) {
	global $db, $user, $aliases;
	$html = '';		//% return value [string]
	$num = 0;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	load list of schools from database, order by weight
	//----------------------------------------------------------------------------------------------
	//$sql = "SELECT count(UID) as numForums, school FROM Forums_Board GROUP BY school";

	$sql = "SELECT count(UID) as numForums, sum(weight) as totalWeight, school FROM Forums_Board "
		 . "GROUP BY school "
		 . "ORDER BY totalWeight";

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
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
