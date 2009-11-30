<?

//-------------------------------------------------------------------------------------------------
//	for each peer we know about, check if we're properly configured
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	only admins can do this
	//---------------------------------------------------------------------------------------------

	echo "<h1>Testing Peer Connectivity</h1>\n";

	$peers = syncListPeers();
	foreach($peers as $peer) {
		echo "Attempting to connect to peer: " . $peer['servername'] . "<br/>\n";
		echo "UID: " . $peer['UID'] . "<br/>\n";
		echo "URL: " . $peer['serverurl'] . "<br/>\n";
	
		$url = $peer['serverurl'] . 'sync/peertest/';
		$result = syncCurlGet($url, $peer['password']);

		$result = str_replace('>', '&gt;', $result);
		$result = str_replace('<', '&lt;', $result);
		echo "result:<br/>\n$result<br/><br/>\n";

	}


?>
