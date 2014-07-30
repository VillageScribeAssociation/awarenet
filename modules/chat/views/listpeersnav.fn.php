<?

	require_once($kapenta->installPath . 'modules/chat/models/peers.set.php');

//--------------------------------------------------------------------------------------------------
//|	list all peers reported by chat server, formatted for nave (300px wide)
//--------------------------------------------------------------------------------------------------

function chat_listpeersnav($args) {
	global $db;
	global $theme;

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//	none as yet

	$set = new Chat_Peers(true);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Peer', 'Sessions');

	foreach($set->members as $item) {
		$peerHtml = ''
		 . $item['peerName'] . "<br/>"
		 . "<small>" . $item['peerUID'] . "</small><br/>"
		 . "<small>" . $item['peerUrl'] . "</small>";

		$sessions = '[[:chat::listactivesessions::peerUID=' . $item['peerUID'] . ':]]';
		$table[] = array($peerHtml, $sessions);
	}

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
