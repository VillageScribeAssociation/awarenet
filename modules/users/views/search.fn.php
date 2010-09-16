<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user search results  // TODO: pagination, fix this up
//--------------------------------------------------------------------------------------------------
//arg: fsearch - query [string]
//opt: pageno - page number (not yet implemented) [string]

function users_search($args) {
	global $db;
	$html = '';		//%	return value [string]

	if (false == array_key_exists('fsearch', $args)) { return ''; }
	if ('' == trim($args['fsearch'])) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make query (this can be much more efficient)
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($args['fsearch']));
	$sql = "select UID, concat(firstname, ' ', surname, ' ', username) as qs from Users_User";
	$result = $db->query($sql);
	$count = 0;

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
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
	
	if ('' == $html) { 
		$html .= "<br/><b>no search results for: " . $args['fsearch'] . "</b><br/>\n";
	} else {
		$html = "<br/><b>$count results</b><br/>\n" . $html;
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
