<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/updates.class.php');

//--------------------------------------------------------------------------------------------------
//*	development / admin script to manually push the next set of messages in the queue
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	header('Content-type: text/plain');

	if ('' == $kapenta->request->ref) { $kapenta->page->doXmlError('Peer not given.'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->doXmlError('Could not load peer.'); }

	$updates = new P2P_Updates($peer->UID);

	$myUID = $kapenta->registry->get('p2p.server.uid');
	if ('' == $myUID) { $kapenta->page->doXmlError('this peer does not have a UID'); }

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
				$encrypted = $peer->pack($raw, $fileName);;

				$url = $peer->url . 'p2p/updatesfrom/' . $myUID;
				echo "pushing to: $url\n";
				echo "transfer size: " . mb_strlen($encrypted, 'ASCII') . " bytes\n";

				$result = $utils->curlPost($url, array('m' => $encrypted));

				echo "PEER RESPONDS:\n\n";
				echo $result . "\n\n";

				if ('<ok/>' == substr($result, -5)) {
					echo "PEER CONFIRMS RECIEPT OF MESSAGES.\n";
					$kapenta->fileDelete($fileName, true);
					echo "Deleted local file: $fileName \n";
				}

				echo $sent = true;

			}
		}
	}

	if (false == $sent) { echo "queue empty"; }
	

?>
