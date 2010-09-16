<?

//-------------------------------------------------------------------------------------------------
//|	displays this peer's current download list
//-------------------------------------------------------------------------------------------------
//TODO: paginate this, use $db->loadRange(...)

function sync_downloadlist($args) {
	global $db, $user, $theme;
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load downloads from the database
	//----------------------------------------------------------------------------------------------
	$sql = "select * from Sync_Download order by status";
	$result = $db->query($sql);

	if ($db->numRows($result) > 0) {
		//-----------------------------------------------------------------------------------------
		//	add all results to a table
		//-----------------------------------------------------------------------------------------
		$ary = array();
		$ary[] = array('File', 'Status', 'Timestamp');
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
			$ary[] = array($row['filename'], $row['status'], $row['timestamp']);
		}

		$html .= "<b>" . $db->numRows($result) . " items in download queue.</b>\n";
		$html .= $theme->arrayToHtmlTable($ary, true, true);

	} else {
		//-----------------------------------------------------------------------------------------
		//	nothing in download queue
		//-----------------------------------------------------------------------------------------
		$html = "<b>(download queue is empty)</b><br/>\n";

	}

	return $html;
}

?>
