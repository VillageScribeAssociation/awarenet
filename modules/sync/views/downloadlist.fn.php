<?

//-------------------------------------------------------------------------------------------------
//	displays this peer's current download list
//-------------------------------------------------------------------------------------------------
//ifgroup: admin

function sync_downloadlist($args) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return ''; }
	$html = '';

	$sql = "select * from downloads order by status";
	$result = dbQuery($sql);

	if (dbNumRows($result) > 0) {
		//-----------------------------------------------------------------------------------------
		//	add all results to a table
		//-----------------------------------------------------------------------------------------
		$ary = array();
		$ary[] = array('File', 'Status', 'Timestamp');
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			$ary[] = array($row['filename'], $row['status'], $row['timestamp']);
		}

		$html .= "<b>" . dbNumRows($result) . " items in download queue.</b>\n";
		$html .= arrayToHtmlTable($ary, true, true);

	} else {
		//-----------------------------------------------------------------------------------------
		//	nothing in download queue
		//-----------------------------------------------------------------------------------------
		$html = "<b>(download queue is empty)</b><br/>\n";

	}

	return $html;
}

?>
