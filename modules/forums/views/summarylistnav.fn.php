<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all forums at a specific school (formatted for nav)
//--------------------------------------------------------------------------------------------------
//arg: school - UID of a school (not recordAlias) [string]

function forums_summarylistnav($args) {
	global $kapenta;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('school', $args)) { return ''; }
	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	load the boards
	//----------------------------------------------------------------------------------------------

	$sql = "select * from forums_board "
		 . "where school='" . $kapenta->db->addMarkup($args['school']) . "' "
		 . "order by weight DESC";

	$result = $kapenta->db->query($sql);

	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) { 
			$row = $kapenta->db->rmArray($row);
			$html .= "[[:forums::summarynav::forumUID=" . $row['UID'] . ":]]\n";
		}	

	} else { $html = "(no forums as yet)<br/>\n"; }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
