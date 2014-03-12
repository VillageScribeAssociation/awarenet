<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list of peers
//--------------------------------------------------------------------------------------------------

function p2p_listpeers($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';						//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return false; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$range = $kapenta->db->loadRange('p2p_peer', '*', '');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	foreach ($range as $item) {
		$table = array();
		$table[] = array('Name', 'Status', 'Pending', 'Actions');

		$model = new P2P_Peer();
		$model->loadArray($item);
		$ext = $model->extArray();

		$name = ''
		 . "<b>" . $item['name'] . "</b><br/>"
		 . "<small>" . $item['url'] . "<br/>" . $item['UID'] . "</small>";

		$actions = $ext['editLink'] .'<br/>'. $ext['delLink'] .'<br/>'. $ext['scanLink'] .'<br/>';
		if ('no' == $item['firewalled']) { $actions .= $ext['testLink'] . '<br/>'; }

		$status = $item['status'] . '<br/>';
		if ('no' == $item['firewalled']) { $status .= "<small>not firewalled</small><br/>"; }
		if ('yes' == $item['firewalled']) { $status .= "<small>firewalled</small><br/>"; }

		$shareBlock = "[[:p2p::stats::peerUID=" . $item['UID'] . ":]]\n";

		$table[] = array($name, $status, $shareBlock, $actions);

		$html .= $theme->arrayToHtmlTable($table, true, true);

		$html .= "<br/>\n";
		$html .= "[[:p2p::listdownloads::peerUID=" . $item['UID'] . ":]]<br/>\n";
	}



	if (0 == count($range)) { $html .= "<div class='inlinequote'>No peers recorded.</div>"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
