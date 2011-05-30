<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//-------------------------------------------------------------------------------------------------
//*	download all outstanding files from a peer
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check reference and admin permissions
	//---------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Peer UID not given.'); }

	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->do404('No such peer'); }

	//---------------------------------------------------------------------------------------------
	//	get a list of all files from the images module and add to download queue
	//---------------------------------------------------------------------------------------------

	$url = $peer->serverurl . 'sync/listfiles/format_csv/';
	echo "[*] Downloading list: $url<br/>\n"; flush();

	$raw = $sync->curlGet($url, $peer->password);
	echo "[i] " . strlen($raw) . " bytes<br/>\n";
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$parts = explode(",", $line, 4);
		if (4 == count($parts)) {
			$refModule = trim($parts[0]);
			$refModel = trim($parts[1]);
			$refUID = trim($parts[2]);
			$fileName = trim($parts[3]);

			echo "[>] $refModule - $refModel - $refUID :: $fileName <br/>\n"; flush();

			if ( (trim($fileName) != '') && (false == $kapenta->fileExists($fileName)) ) {
				//----------------------------------------------------------------------------------
				// filename is valid and does not exist on this server
				//----------------------------------------------------------------------------------
				//$sync->requestFile($fileName);
				$fileUrl = $peer->serverurl . $fileName;
				$raw = $sync->curlGet($fileUrl, $peer->password);
				echo "[*] Downloaded: " . $fileUrl . " (" . strlen($raw) . " bytes)<br/>\n"; flush();
				$allOk = true;

				if ('' == $raw) { $allOk = false; }
				//if ((true == $allOk) && (false === @imagecreatefromstring($raw))) { $allOk = false; }

				if (true == $allOk) {
					$kapenta->filePutContents($fileName, $raw, true);
					echo "[i] Saved $fileName <br/>\n"; flush();
				}

			} else { 
				echo "[*] Exists: " . $fileName . "<br/>\n"; flush();
			}
		}
	}

	//$page->do302('sync/downloads/');	

?>
