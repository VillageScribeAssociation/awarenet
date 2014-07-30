<?

//--------------------------------------------------------------------------------------------------
//|	nav display of unread chat messages for the current user
//--------------------------------------------------------------------------------------------------

function chat_unreadnav($args) {
	global $user;
	global $theme;
	global $db;

	$outstanding = array();			//% roomUID => messageCount [array]
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $user->role) || ('banned' == $user->role)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	count unread messages for each room this user is a member of
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "user='" . $db->addMarkup($user->UID) . "'";
	// ^ add any further conditions here

	$range = $db->loadRange('chat_membership', '*', $conditions);
	foreach($range as $item) {
		//------------------------------------------------------------------------------------------
		//	check for undelivered messages to this user from this room
		//------------------------------------------------------------------------------------------
		$conditions = array();
		$conditions[] = "room='" . $db->addMarkup($item['room']) . "'";
		$conditions[] = "toUser='" . $db->addMarkup($user->UID) . "'";
		$conditions[] = "delivered='no'";

		$count = $db->countRange('chat_inbox', $conditions);
		if ($count > 0) { $outstanding[$item['room']] = $count; }
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	if (0 == count($outstanding)) { return ''; }

	$html .= "[[:theme::navtitlebox::label=Unread Chat Messages:]]";

	$table = array();
	$table[] = array('Room', '#');

	foreach($outstanding as $roomUID => $messageCount) {
		$table[] = array("[[:chat::roomlink::roomUID=$roomUID:]]", $messageCount);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);	
	$html .= "<br/>\n";

	return $html;
}


?>
