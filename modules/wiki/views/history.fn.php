<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/inc/wikicode.class.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	history summary formatted for nav // TODO: pagination
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of wiki entry [string]
//opt: num - number of recent entries to show (default 1000) [string]

function wiki_history($args) {
	global $db;

	$num = 1000; $html = ''; $total = '0';
	if (false == array_key_exists('UID', $args)) { return false; }
	if (true == array_key_exists('num', $args)) { $num = $args['num']; }

	//----------------------------------------------------------------------------------------------
	//	count all revisions to this article
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "articleUID='" . $db->addMarkup($args['UID']) . "'";
	$total = $db->countRange('Wiki_Revision', $conditions);


	//----------------------------------------------------------------------------------------------
	//	make list of recent revisions
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('Wiki_Revision', '*', $conditions, 'editedOn DESC', (int)$num);

	foreach ($range as $row) {
		$reason = '';

		if ('' != trim($row['reason'])) 
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
