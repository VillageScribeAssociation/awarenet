<?

	require_once($installPath . 'modules/users/models/friendships.mod.php');
	require_once($installPath . 'modules/users/models/users.mod.php');

//--------------------------------------------------------------------------------------------------
//	user search results
//--------------------------------------------------------------------------------------------------
// * $args['fsearch'] = query
// * $args['pageno'] = page number

function users_search($args) {
	$html = '';
	if (array_key_exists('fsearch', $args) == false) { return false; }
	if (trim($args['fsearch']) == '') { return false; }

	//----------------------------------------------------------------------------------------------
	//	make query (this can be much more efficient)
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($args['fsearch']));
	$sql = "select UID, concat(firstname, ' ', surname, ' ', username) as qs from users";
	$result = dbQuery($sql);
	$count = 0;

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$matchRow = true;
		$qs = strtolower($row['qs']);

		foreach($parts as $part) {
		  if (($part != '') AND (strpos(' ' . $qs, $part) == false)) { $matchRow = false; }
		}	

		if ($matchRow == true) {
			$friendUrl = '%%serverPath%%users/find/add_' . $row['UID'];
			$html .= "[[:users::summarynav::userUID=" . $row['UID'] . "::target=main:]]\n";
			$html .= "<a href='" . $friendUrl . "'>[add as friend >> ]</a><br/><hr/>\n";
			$count++;
		}
	}
	
	if ($html == '') {
		$html .= "<br/><b>no search results for: " . $args['fsearch'] . "</b><br/>\n";
	} else {
		$html = "<br/><b>$count results</b><br/>\n" . $html;
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>