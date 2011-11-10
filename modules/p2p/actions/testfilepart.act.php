<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/client.class.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	development action to test download of a file part from a trusted peer
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$fileName = 'data/videos/1/1/0/110908755616157252';

	$peer = new P2P_Peer('709366884309396553');
	$klf = new KLargeFile($fileName);

	echo "<h1>" . $klf->path . "</h1>\n"; flush();

	foreach($klf->parts as $part) {
	  $idx = (int)$part['index'];
	  //if (4 == $idx) {

		echo "<h2>Downloading part " . $part['index'] . "</h2>";

		$message = "
			<part>
				<path>" . $klf->path . "</path>
				<index>" . $idx . "</index>
				<hash>" . $part['hash'] . "</hash>
				<status>" . $part['status'] . "</status>
				<size>" . $part['size'] . "</size>
				<fileName>" . $part['fileName'] . "</fileName>
			</part>
		";

		$part64 = $peer->sendMessage('filepart', $message);
		echo "recieved: " . strlen($part64) . " bytes.<br/>";

		$partRaw = base64_decode($part64);
		echo "decoded to: " . strlen($partRaw) . " bytes.<br/>";

		$sha1 = sha1($partRaw);
		if ($part['hash'] == $sha1) {
			echo "HASHES MATCH<br/>";

			$check = $klf->storePart($idx, $part64, $sha1);
			if (true == $check) {
				echo "Part " . $part['index'] . " stored on disk <br/>\n";
			} else {
				echo "Part " . $part['index'] . " NOT stored on disk <br/>\n";
			}

		} else { 
			echo "HASH MISMATCH<br/>";
			echo "data: $sha1<br/>";
			echo "part: " . $part['hash'] . "<br/>";
		}

		//------------------------------------------------------------------------------------------
		//	save the part to disk
		//------------------------------------------------------------------------------------------
		flush();
	 //}
	}
	
	/*
	$message = "
		<part>
			<path>$fileName</path>
			<index>26</index>
			<hash>$hash</hash>
			<status>pending</status>
			<size>524288</size>
			<fileName>data/transfer/parts/1314881342_e602357495c5bb550a684ae9385eb4bebd183f13.part.php</fileName>
		</part>
	";
	*/

?>
