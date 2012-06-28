<?

//--------------------------------------------------------------------------------------------------
//*	summarize daily user activity for twitter microreport
//--------------------------------------------------------------------------------------------------
//opt: date - date to be shown, default is today, YYYY-MM-DD [string]

function users_twitterdaily($args) {
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
	$newUser = $db->countRange('users_user', $conditions);
	$newSession = $db->countRange('users_session', $conditions);
	$newFriendship = $db->countRange('users_friendship', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the snippet
	//----------------------------------------------------------------------------------------------
	if ($newFriendship > 0) { $txt .= " Friendships: " . $newFriendship; }
	if ($newUser > 0) { $txt .= " New users: " . $newUser; }
	if ($newSession > 0) { $txt .= " Active users: " . $newSession; }

	return $txt;
}

?>
