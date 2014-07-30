<?

//--------------------------------------------------------------------------------------------------
//|	makes a table showing all awareNet instances this chat server recognizes
//--------------------------------------------------------------------------------------------------

function chatserver_listpeers($args) {
	global $user;
	global $theme;
	global $db;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	// ^ add any conditions here
	$range = $db->loadRange('chatserver_peer', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('UID', 'PeerUID', 'Name', 'Url', '[x]');

	foreach($range as $item) {
		$delUrl = '%%serverPath%%chatserver/removepeer/' . $item['UID'];
		$delLink = "<a href='" . $delUrl . "'>[remove]</a>";
		$table[] = array($item['UID'], $item['peerUID'], $item['name'], $item['url'], $delLink);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);
	if (0 == count($range)) { $html .= "<div class='inlinequote'>None yet added.</div>"; }
	return $html;
}

?>
