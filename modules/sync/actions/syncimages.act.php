<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//--------------------------------------------------------------------------------------------------
//*	copy any outstanding images from another peer
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and reference
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('Peer not speicifed.'); }

	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->do404('No such peer.'); }

	//----------------------------------------------------------------------------------------------
	//	find all outstanding images, excluding transforms
	//----------------------------------------------------------------------------------------------
	$sql = "select * from images_image where refModel != 'images_image'";
	$result = $db->query($sql);
	while($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		if (('' != $row['fileName']) && (false == $kapenta->fileExists($row['fileName']))) {
			echo "[i] File does not exist on server: " . $row['fileName'] . "<br/>\n"; flush();
			$imgUrl = $peer->serverurl . $row['fileName'];
			$raw = $utils->curlGet($imgUrl);
			echo "[*] Downloaded $imgUrl (" . strlen($raw) . " bytes)<br/>\n";
			$allOk = true;

			if (('' != $row['hash']) && (sha1($raw) != $row['hash'])) { 
				$msg = "Error downloading file $imgUrl (hash mismatch)<br/>";
				$kapenta->logSync($msg);
				echo $msg . "\n"; flush();
				$allOk = false;
			} 

			if ('' == $raw) { $allOk = false; }

			if ((true == $allOk) && (false === @imagecreatefromstring($raw))) {
				$msg = "Error downloading file $imgUrl (not a valid image)<br/>";
				$kapenta->logSync($msg);
				echo $msg . "\n"; flush();
				$allOk = false;
			}

			if (true == $allOk) {
				$kapenta->filePutContents($row['fileName'], $raw);
				$kapenta->logSync("Downloaded image $imgUrl (OK)<br/>");
				echo "Downloaded image $imgUrl (object " . $row['UID'] . ")(OK)<br/>\n"; flush();
			}
		}
	}
	

?>
