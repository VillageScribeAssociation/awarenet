<?php

//--------------------------------------------------------------------------------------------------
//* Download any outstanding files from peer
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check arguments and user roles
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('Peer UID not given'); }

	$peer = new P2P_Peer($kapenta->request->ref);
	if (false == $peer->loaded) { $kapenta->page->do404('Peer Not found.'); }

	//----------------------------------------------------------------------------------------------
	//	find all objects which have 'fileName' and 'hash' fields
	//----------------------------------------------------------------------------------------------

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

                    if ('' === $item['fileName']) { $want = false; }
                    if (-1 !== strpos($item['fileName'], 'videos')) { $want = false; }
    
					if (true == $want) {

                        $fullUrl = $peer->url . $item['fileName'];
                        $localFile = $kapenta->installPath . $item['fileName'];

						$msg = ''
						 . "Downloading missing file:<br/>\n"
                         . "Peer: " . $peer->url . "<br/>\n"
						 . "Owner: " . $tableName . "::" . $item['UID'] . "<br/>\n"
						 . "SHA1 Hash: " . $item['hash'] . "<br/>\n"
						 . "Canonical: " . $item['fileName'] . "<br/>\n"
						 . "URL: " . $fullUrl . "<br/>\n"
						 . "local: " . $localFile . "<br/>\n"
                         . "<br/>\n";

                        echo $msg; flush();

                        $kapenta->fs->makePath($item['fileName'], true);

                        $raw = $kapenta->utils->curlGet($fullUrl);
                        
                        echo "downloaded: " . strlen($raw) . " bytes<br/>\n";

                        if (strlen($raw) > 1024) {
                            $check = $kapenta->fs->put($item['fileName'], $raw, true);

                            if (true == $check) {
                                echo "saved: " . $localFile . "<br/>\n";
                            } else {
                                echo ''
                                 . "could not save: " . $localFile
                                 . " (" . $kapenta->fs->lastErr . ")<br/>\n";
                            }

                        }

                        echo "<hr/>\n";

                        //die();

					} // end if want

                    


				} // end if file is missing

			} // end while iterating through results

		} // end if has fileName and hash

	} // end foreah table

	$kapenta->page->do302('p2p/peers/');

?>

?>
