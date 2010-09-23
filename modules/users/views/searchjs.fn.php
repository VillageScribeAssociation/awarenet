<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	user search results, javascript format
//--------------------------------------------------------------------------------------------------
//arg: squery - query [string]
//arg: callback - javascript function on parent window - fn(UID, uHtml, uName, uThumb) {} [string]
//arg: pageno - page number (To be implemented) [string]
//TODO: fix this up

function users_searchjs($args) {
	global $db, $theme, $utils;
	$js = '';

	if (false == array_key_exists('squery', $args)) { return ''; }
	if ('' == trim($args['squery'])) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make query (this can be much more efficient)
	//----------------------------------------------------------------------------------------------
	$parts = explode(' ', strtolower($args['squery']));
	$sql = "select UID, concat(firstname, ' ', surname, ' ', username) as qs "
		 . "from Users_User order by firstname, surname";

	$result = $db->query($sql);
	$count = 0;

	$js = "<script src='%%serverPath%%modules/users/js/usersearch.js'></script>\n";
	$js .= "<div id='userSearchResults'>Searching '" . $args['squery'] . "'... </div>";
	$js .= "<script>\n";
	$js .= "var userList = new Array();\n";

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$matchRow = true;
		$qs = ' ' . strtolower($row['qs']);

		foreach($parts as $part) 
			{ if (($part != '') AND (strpos($qs, $part) == false)) { $matchRow = false; }	}	

		if (true == $matchRow) {
			$uUID = $row['UID'];
			$uName = $theme->expandBlocks("[[:users::name::userUID=". $uUID .":]]", '');
			$uUrl = "%%serverPath%%users/profile/"  . $row['alias'];
			$uHtml = $theme->expandBlocks("[[:users::summarynav::userUID=". $uUID ."::target=_parent:]]\n", '');
			$uThumb = $theme->expandBlocks("[[:users::avatar::userUID=". $uUID ."::size=thumb90::link=no:]]\n", '');

			$js .= "userList[" . $count . "] = new Array('" . $utils->jsMarkup($uUID) . "', '" 
															. $utils->jsMarkup($uName) . "', '" 
															. $utils->jsMarkup($uUrl) . "', '" 
															. $utils->jsMarkup($uThumb) . "', '" 
															. $utils->jsMarkup($uHtml) . "');\n";

			$count++;
		}
	}

	$js .= "renderResultsHtml(0);";
	$js .= "</script>\n";	

	return $js;
}


?>
