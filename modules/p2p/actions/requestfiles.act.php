<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//-------------------------------------------------------------------------------------------------
//*	action to request outstanding files from peer
//-------------------------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------------------------
	//	check arguments and user roles
	//-------------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Peer UID not given'); }

	$model = new P2P_Peer($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Peer Not found.'); }

	//-------------------------------------------------------------------------------------------------
	//	find all objects which have 'fileName' and 'hash' fields
	//-------------------------------------------------------------------------------------------------

	$tables = $kapenta->db->listTables();

	foreach($tables as $tableName) {
		$dbSchema = $kapenta->db->getSchema($tableName);
		if (
			(true == array_key_exists('fileName', $dbSchema['fields'])) &&
			(true == array_key_exists('hash', $dbSchema['fields']))
		) {
			
			$sql = "select * from $tableName";
			$result = $kapenta->db->query($sql);
			while ($row = $kapenta->db->fetchAssoc($result)) {
				$item = $kapenta->db->rmArray($row);
				if (false == $kapenta->fs->exists($item['fileName'])) {
					$msg = "Missing: " . $item['fileName'] . "<br/>";
					$want = true;

					//	no hash
					if ('' == $item['hash']) {
						$want = false;
						$session->msgAdmin($msg . "Not requesting (no hash).");
					}

					$metaFile = ''
					 . 'data/p2p/transfer/meta/'
					 . $item['UID'] . '_' . $item['hash'] . '.xml.php';

					//	already downloading
					if (true == $kapenta->fs->exists($metaFile)) {
						$want = false;
						$session->msgAdmin($msg . "Already downloading.");
					}

					if (true == $want) {

						$msg = ''
						 . "Requesting missing file:<br/>\n"
						 . "Owner: " . $tableName . "::" . $item['UID'] . "<br/>\n"
						 . "SHA1 Hash: " . $item['hash'] . "<br/>\n"
						 . "Canonical: " . $item['fileName'] . "<br/>\n";
						$session->msgAdmin($msg);

						$xml = ''
						 . "<filemetarequest>\n"
						 . "  <peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
						 . "  <model>$tableName</model>\n"
						 . "  <uid>" . $item['UID'] . "</uid>\n"
						 . "  <filename>" . $item['fileName'] . "</filename>\n"
						 . "  <hash>" . $item['hash'] . "</hash>\n"
						 . "</filemetarequest>\n";

						$detail = array(
							'message' => $xml,
							'peer' => $model->UID,
							'priority' => '6'
						);
					
						$kapenta->raiseEvent('p2p', 'p2p_narrowcast', $detail);

					} // end if want

				} // end if file is missing

			} // end while iterating through results

		} // end if has fileName and hash

	} // end foreah table

	$kapenta->page->do302('p2p/peers/');

?>
