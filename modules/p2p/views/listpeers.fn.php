<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary list of peers
//--------------------------------------------------------------------------------------------------

function p2p_listpeers($args) {
		global $kapenta;
		global $user;
		global $theme;

	$html = '';						//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return false; }

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

		$model = new P2P_Peer($item['UID']);
		$ext = $model->extArray();

		$name = ''
		 . "<b>" . $item['name'] . "</b><br/>"
		 . "<small>" . $item['url'] . "<br/>" . $item['UID'] . "</small>";

		$actions = $ext['editLink'] .'<br/>'. $ext['delLink'] .'<br/>'. $ext['scanLink'] .'<br/>';
		if ('no' == $item['firewalled']) { $actions .= $ext['testLink'] . '<br/>'; }

		$status = '';	//$item['status'] . '<br/>';

		$status .= ''
		 . "[[:p2p::firewallform"
		 . "::peerUID=" . $item['UID']
		 . "::firewalled=" . $item['firewalled']
		 . ":]]";

		$shareBlock = "[[:p2p::stats::peerUID=" . $item['UID'] . ":]]\n";

		$table[] = array($name, $status, $shareBlock, $actions);

		$labels = array(
			'pending' => $theme->arrayToHtmlTable($table, true, true),
			'UID' => $item['UID']
		);

		$block = $theme->loadBlock('modules/p2p/views/showpeer.block.php');
		$html .= $theme->replaceLabels($labels, $block);

	}


	if (0 == count($range)) { $html .= "<div class='inlinequote'>No peers recorded.</div>"; }
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
