<?

	require_once($installPath . 'modules/wiki/models/wiki.mod.php');
	require_once($installPath . 'modules/wiki/models/wikicode.mod.php');
	require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	history summary formatted for nav
//--------------------------------------------------------------------------------------------------
// * $args['UID'] = UID of wiki entry
// * $args['num'] = number of recent entries to show

function wiki_history($args) {
	$num = 1000; $html = ''; $total = '0';
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

		$html .= "edit by: [[:users::fullname::userUID=" . $row['editedBy'] . ":]]<br/>"
			  . $reason . $row['type'] . ": " . $row['editedOn'] . "<br/>\n"
			  . "<hr>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	link to further revisions
	//----------------------------------------------------------------------------------------------

	$html .= "Total revisions: $total <br/>\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>