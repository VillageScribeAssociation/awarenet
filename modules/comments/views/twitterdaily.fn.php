<?

//--------------------------------------------------------------------------------------------------
//*	summarize daily comment activity for twitter microreport
//--------------------------------------------------------------------------------------------------
//opt: date - date to be shown, default is today, YYYY-MM-DD [string]

function comments_twitterdaily($args) {
	global $db;
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
	$conditions = array("DATE(createdOn) = '" . $db->addMarkup($date) . "'");
	$newComments = $db->countRange('comments_comment', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the snippet
	//----------------------------------------------------------------------------------------------
	if ($newComments > 0) { $txt .= " Comments: " . $newComments; }

	return $txt;
}

?>
