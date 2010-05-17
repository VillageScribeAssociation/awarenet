<?

	require_once($installPath . 'modules/users/models/friendship.mod.php');
	require_once($installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user search results, javascript format
//--------------------------------------------------------------------------------------------------
//arg: squery - query [string]
//arg: callback - javascript function on parent window - fn(UID, uHtml, uName, uThumb) {} [string]
//arg: pageno - page number (To be implemented) [string]

function users_searchjs($args) {
	$js = '';
	if (array_key_exists('squery', $args) == false) { return false; }
	if (trim($args['squery']) == '') { return false; }

	//----------------------------------------------------------------------------------------------
	//	make query (this can be much more efficient)
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($args['squery']));
	$sql = "select UID, concat(firstname, ' ', surname, ' ', username) as qs from users order by firstname, surname";
	$result = dbQuery($sql);
	$count = 0;

	$js = "<script src='%%serverPath%%modules/users/js/usersearch.js'></script>\n";
	$js .= "<div id='userSearchResults'>Searching '" . $args['squery'] . "'... </div>";
	$js .= "<script>\n";
	$js .= "var userList = new Array();\n";

	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$matchRow = true;
		$qs = ' ' . strtolower($row['qs']);

		foreach($parts as $part) 
			{ if (($part != '') AND (strpos($qs, $part) == false)) { $matchRow = false; }	}	

		if ($matchRow == true) {
			$uUID = $row['UID'];
			$uName = expandBlocks("[[:users::name::userUID=". $uUID .":]]", '');
			$uUrl = "%%serverPath%%users/profile/"  . $row['recordAlias'];
			$uHtml = expandBlocks("[[:users::summarynav::userUID=". $uUID ."::target=_parent:]]\n", '');
			$uThumb = expandBlocks("[[:users::avatar::userUID=". $uUID ."::size=thumb90::link=no:]]\n", '');

			$js .= "userList[" . $count . "] = new Array('" . jsMarkup($uUID) . "', '" 
															. jsMarkup($uName) . "', '" 
															. jsMarkup($uUrl) . "', '" 
															. jsMarkup($uThumb) . "', '" 
															. jsMarkup($uHtml) . "');\n";

			$count++;
		}
	}

	$js .= "renderResultsHtml(0);";
	$js .= "</script>\n";	

	return $js;
}


?>
