<?

//--------------------------------------------------------------------------------------------------
//*	summarize daily forum activity for twitter microreport
//--------------------------------------------------------------------------------------------------
//opt: date - date to be shown, default is today, YYYY-MM-DD [string]

function forums_twitterdaily($args) {
	global $kapenta;
	global $kapenta;

	$date = substr($kapenta->datetime(), 0, 10);
	$txt = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('date', $args)) { $date = $args['date']; }

	//----------------------------------------------------------------------------------------------
	//	count new objects
	//----------------------------------------------------------------------------------------------
	$conditions = array("DATE(createdOn) = '" . $kapenta->db->addMarkup($date) . "'");
	$newThreads = $kapenta->db->countRange('forums_thread', $conditions);
	$newReplies = $kapenta->db->countRange('forums_reply', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the snippet
	//----------------------------------------------------------------------------------------------
	if ($newThreads > 0) { $txt .= " Forum threads: " . $newThreads; }
	if ($newReplies > 0) { $txt .= " Forum replies: " . $newReplies; }

	return $txt;
}

?>
