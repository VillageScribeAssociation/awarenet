<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	history summary formatted for nav
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of wiki entry [string]
//opt: num - number of recent entries to show (default is 10) [string]

function wiki_historynav($args) {
	global $db;

	$num = 10; $html = ''; $total = '0';
	if (array_key_exists('UID', $args) == false) { return false; }
	if (array_key_exists('num', $args) == true) { $num = $args['num']; }

	//----------------------------------------------------------------------------------------------
	//	count all revisions to this article
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "articleUID='" . $db->addMarkup($args['UID']) . "'";

	//$sql = "select count(UID) as revcount "
	//	 . "from Wiki_Revision "
	//	 . "where UID='" . $db->addMarkup($args['UID']) . "'";

	//$result = $db->query($sql);
	//$row = $db->fetchAssoc($result);
	$total = $db->countRange('Wiki_Revision', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make list of recent revisions
	//----------------------------------------------------------------------------------------------

	$range = $db->loadRange('Wiki_Revision', '*', $conditions, 'editedOn DESC', (int)$num);

	//$sql = "select * from Wiki_Revision "
	//	 . "where UID='" . $db->addMarkup($args['UID']) . "' "
	//	 . "order by editedOn DESC limit $num";

	foreach ($range as $row) {
		$reason = '';

		if ('' != trim($row['reason'])) 
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
