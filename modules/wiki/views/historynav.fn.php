<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	history summary formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of wiki entry [string]
//opt: num - number of recent entries to show (default is 10) [string]

function wiki_historynav($args) {
	$num = 10; $html = ''; $total = '0';
	if (array_key_exists('UID', $args) == false) { return false; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	//----------------------------------------------------------------------------------------------
	//	count all revisions to this article
	//----------------------------------------------------------------------------------------------

	$sql = "select count(UID) as revcount "
		 . "from wikirevisions where refUID='" . sqlMarkup($args['UID']) . "'";

	$result = dbQuery($sql);
	$row = dbFetchAssoc($result);
	$total = $row['revcount'];

	//----------------------------------------------------------------------------------------------
	//	make list of recent revisions
	//----------------------------------------------------------------------------------------------

	$sql = "select * from wikirevisions "
		 . "where refUID='" . sqlMarkup($args['UID']) . "' "
		 . "order by editedOn DESC limit $num";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
		$row = sqlRMArray($row);
		$reason = '';

		if (trim($row['reason']) != '') 
			{ $reason = "reason: <i>" . $row['reason'] . "</i><br/>\n"; }

		$html .= "<small>edit by: [[:users::fullname::userUID=" . $row['editedBy'] . ":]]<br/>"
			  . $reason . $row['type'] . ": " . $row['editedOn'] . "<br/>\n"
			  . "</small><hr>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	link to further revisions
	//----------------------------------------------------------------------------------------------

	$html .= "<small>Total revisions: $total "
		   . "<a href='%%serverPath%%wiki/history/" . $args['UID'] . "'>"
		   . "[view all]</a><br/><br/></small>\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
