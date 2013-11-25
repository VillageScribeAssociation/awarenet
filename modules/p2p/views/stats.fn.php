<?

//--------------------------------------------------------------------------------------------------
//|	basic stats about the p2p_gifts table
//--------------------------------------------------------------------------------------------------
//opt: peerUID - UID of a P2P_Peer object [string]

function p2p_stats($args) {
	global $kapenta;
	global $kapenta;
	global $user;
	global $theme;
	global $db;

	$html = '';								//%	return value [string]
	$filter = '';							//%	result set filter [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('peerUID', $args)) { return '(peerUID not given)'; }
	if ('admin' != $user->role) { return ''; }

	//TODO: check and sanitize peer UID

	$queueDir = 'data/p2p/pending/' . $args['peerUID'] . '/';

	//----------------------------------------------------------------------------------------------
	//	summarize outgoing message queue
	//----------------------------------------------------------------------------------------------
	$priorities = array();
	$table = array();
	$table[] = array('Priority', 'Bytes');

	$files = $kapenta->listFiles($queueDir);
	sort($files);

	foreach($files as $file) {
		if (('.' != $file) && ('..' != $file)) {
			$parts = explode('.', $file);
			if (false == array_key_exists($parts[0], $priorities)) { $priorities[$parts[0]] = 0; }
			$priorities[$parts[0]] += $kapenta->fs->size($queueDir . $file);			
		}
	}

	foreach($priorities as $priority => $total) {
		$table[] = array($priority, (string)$total);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;

}

?>
