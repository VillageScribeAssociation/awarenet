<?

//--------------------------------------------------------------------------------------------------
//*	summarize daily user activity for twitter microreport
//--------------------------------------------------------------------------------------------------
//opt: date - date to be shown, default is today, YYYY-MM-DD [string]

function users_twitterdaily($args) {
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
	$newUser = $kapenta->db->countRange('users_user', $conditions);
	$newSession = $kapenta->db->countRange('users_session', $conditions);
	$newFriendship = $kapenta->db->countRange('users_friendship', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the snippet
	//----------------------------------------------------------------------------------------------
	if ($newFriendship > 0) { $txt .= " Friendships: " . $newFriendship; }
	if ($newUser > 0) { $txt .= " New users: " . $newUser; }
	if ($newSession > 0) { $txt .= " Active users: " . $newSession; }

	return $txt;
}

?>
