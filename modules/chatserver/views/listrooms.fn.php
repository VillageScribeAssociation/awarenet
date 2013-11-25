<?

//--------------------------------------------------------------------------------------------------
//|	list all chat rooms maintained on this server
//--------------------------------------------------------------------------------------------------

function chatserver_listrooms($args) {
	global $user;
	global $theme;
	global $db;

	$html = '';							//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	// ^^ add any argument checks here

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array("status='global'");
	$range = $db->loadRange('chatserver_room', '*', $conditions, 'title');
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('UID', 'Room', 'Members');
	foreach($range as $item) {
		$roomUrl = '%%serverPath%%chatserver/showroom/' . $item['UID'];
		$roomLink = "<a href='" . $roomUrl . "'>" . $item['title'] . "</a>";
		$table[] = array($item['UID'], $roomLink, $item['memberCount']);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
