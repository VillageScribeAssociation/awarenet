<?php

	require_once('../../../shinit.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/worker.class.php');

//--------------------------------------------------------------------------------------------------
//*	administrative shell script to directly download add images from a peer
//--------------------------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------------------------
	//	check arguments and user roles
	//-------------------------------------------------------------------------------------------------
	//if ('admin' != $user->role) { $page->do403(); }
	//if ('' == $kapenta->request->ref) { $page->do404('Peer UID not given'); }

	$usage_notes = ''
	 . "Usage: pullfiles.php [peerUID]|[peerName]|[peerUrl]\n"
	 . "This script will repeatedly poll a peer for new objects.\n\n";

	if (1 == count($argv)) { echo $argv[1] . "\n"; }

	$range = $db->loadRange('p2p_peer', '*');
	$peerUID = '';

	foreach($range as $item) {
		if ($item['UID'] == $argv[1]) { $peerUID = $item['UID']; }
 		if (strtolower($item['name']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		if (strtolower($item['url']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		$usage_notes .= "peer: " . $item['UID'] . ' - ' . $item['name'] . " - " . $item['url'] . "\n";
	}
	$usage_notes .= "\n\n";

	if ('' == $peerUID) { echo $usage_notes; die(); }

	$model = new P2P_Peer($peerUID);
	if (false == $model->loaded) { echo $usage_notes; die(); }

	//-------------------------------------------------------------------------------------------------
	//	find all objects which have 'fileName' and 'hash' fields
	//-------------------------------------------------------------------------------------------------

	$tables = $db->listTables();

	foreach($tables as $tableName) {
		$dbSchema = $db->getSchema($tableName);
		if (
			(true == array_key_exists('fileName', $dbSchema['fields'])) &&
			(true == array_key_exists('hash', $dbSchema['fields']))
		) {
			
			$sql = "select * from $tableName";
			$result = $db->query($sql);
			while ($row = $db->fetchAssoc($result)) {
				$item = $db->rmArray($row);
				if (false == $kapenta->fs->exists($item['fileName'])) {
					$msg = "Missing: " . $item['fileName'] . "<br/>";
					$want = true;

					//	no hash
					if ('' == $item['hash']) {
						$want = false;
						$session->msgAdmin($msg . "Not requesting (no hash).");
					}

					if (false != strpos($item['fileName'], 'khan/')) { $want = false; }

					if (true == $want) {

						$msg = ''
						 . "Requesting missing file:<br/>\n"
						 . "Owner: " . $tableName . "::" . $item['UID'] . "\n"
						 . "SHA1 Hash: " . $item['hash'] . "\n"
						 . "Canonical: " . $item['fileName'] . "\n";

						echo $msg;

						$kapenta->fileMakeSubdirs($item['fileName']);

						$localFile = $kapenta->installPath . $item['fileName'];
						$remoteFile = $model->url . $item['fileName'];
	
						$shellCmd = "wget --output-document=\"" . $localFile . "\" \"" . $remoteFile . "\"";

						echo $shellCmd . "\n";
						echo shell_exec($shellCmd);

						if ((false == file_exists($localFile)) || (0 === filesize($localFile))) {
							echo "Could not download: $remoteFile \n";
							if (true == file_exists($localFile)) { unlink($localFile); }
						}

					} // end if want

				} // end if file is missing

			} // end while iterating through results

		} // end if has fileName and hash

	} // end foreah table

	echo "Done. You may wish to change permissions on downloaded files.\n";

?>
