<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums, grouped by school (noargs)
//--------------------------------------------------------------------------------------------------
//opt: num - max number of threads to show per board (int) [string]

function forums_summarylistall($args) {
	global $kapenta;
	global $user;
	global $aliases;
	global $theme;

	$count = 0;		//%	number of boards returned [int]
	$num = 0;		//%	max number of threads to show per board [int]
	$html = '';		//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	load list of schools from database, order by weight
	//----------------------------------------------------------------------------------------------
	//TODO: figure a way around this awkward query

	$sql = "SELECT count(UID) as numForums, sum(weight) as totalWeight, school FROM forums_board "
		 . "GROUP BY school "
		 . "ORDER BY totalWeight";

	$result = $kapenta->db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	while ($row = $kapenta->db->fetchAssoc($result)) { 
		$row = $kapenta->db->rmArray($row);
		$schoolName = '';
		if ('' == $row['school']) { $schoolName = 'General'; }
		else {
			$nameBlock = '[[:schools::name::schoolUID=' . $row['school'] . ':]]';
			$schoolName = $theme->expandBlocks($nameBlock);
		}

		$html .= ''
		 . "[[:theme::navtitlebox::width=570::label=$schoolName:]]\n"
		 . "<div class='spacer'></div>\n"
		 . "[[:forums::summarylist::school=" . $row['school'] . ":]]\n";

		$count++;
	}	

	if (0 == $count) { return "<div class='inlinequote'>no forums yet</div><br/>"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
