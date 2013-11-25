<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	return an encrypted set of updates for a given peer
//--------------------------------------------------------------------------------------------------

	header('Content-type: text/plain');

	if ('' == $kapenta->request->ref) { $page->doXmlError('Peer not given.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $page->doXmlError('Could not load peer.'); }

	$updates = new P2P_Updates($peer->UID);

	//----------------------------------------------------------------------------------------------
	//	first look for any locked files, by priority (complete batches, more efficent)
	//----------------------------------------------------------------------------------------------
	$files = $updates->listFiles();
	$sent = false;

	for ($priority = 0; $priority < 10; $priority++) {
		foreach($files as $fileName => $meta) {
			if (($priority == (int)$meta['priority']) && (false == $sent)) {
				
				//----------------------------------------------------------------------------------
				//	have found a file to send, lock it if not already done
				//----------------------------------------------------------------------------------
				if ('no' == $meta['locked']) {
					$updates->lockFile($fileName);
					$fileName = str_replace('.xml.txt', '.xml.bz2', $fileName);
				}

				//----------------------------------------------------------------------------------
				//	encrypt with this peer's public key and send it
				//----------------------------------------------------------------------------------
				$raw = $kapenta->fs->get($fileName);
				echo $peer->pack($raw, $fileName);
				$sent = true;

			}
		}
	}

	if (false == $sent) { echo "queue empty"; }

?>
