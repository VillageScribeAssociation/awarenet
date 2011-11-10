<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//|	shows a list of files this peer is downloading
//--------------------------------------------------------------------------------------------------
//arg: peerUID - UID of a P2P_Peer object [string]

function p2p_listdownloads($args) {
	global $kapenta;
	global $user;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('peerUID', $args)) { return '(unknown peer)'; }

	$downloads = new P2P_Downloads($args['peerUID']);	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	if (0 == count($downloads->members)) { 
		$html .= "<div class='inlinequote'>No active downloads.</div>\n";
	} else {
		$html .= "<div class='inlinequote'>" . count($downloads->members) . " downloads.</div>";
	}

	foreach($downloads->members as $fileName) {
		$html .= "[[:p2p::download::path=" . $fileName . ":]]\n";
	}

	return $html;
}

?>
