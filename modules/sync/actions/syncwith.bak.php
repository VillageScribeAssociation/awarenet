<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//--------------------------------------------------------------------------------------------------
//	perform a complete sync with another peer (check all tables and files)
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->doXmlError('only admins may do this'); }

	//----------------------------------------------------------------------------------------------
	//	load the server's record
	//----------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->doXmlError('peer not supplied'); }
	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->doXmlError('peer not found'); }

	echo "<h1>Sync With: " . $peer->servername . "</h1>\n"; flush();

	//----------------------------------------------------------------------------------------------
	//	make list of tables to sync
	//----------------------------------------------------------------------------------------------
	$syncTables = array();
	$tables = $db->loadTables();
	foreach($tables as $table) { 
		if ((strpos($table, '_') != false) && (false == in_array($table, $sync->ignoreTables))) {
			$syncTables[] = $table;
		} 
	}	


	foreach($syncTables as $table) {
		$localSchema = $db->getSchema($table);

		//------------------------------------------------------------------------------------------
		//	get list of all objects in this (remote) table from peer
		//------------------------------------------------------------------------------------------
		echo "<h2>Checking: $table</h2>\n"; flush();

		$changes = $sync->getTableLe($peer->UID, $table);

		echo "[i] Total Objects: " . $changes['total'] . "<br/>\n";
		echo "[i] Dirty Objects: " . $changes['dirty'] . "<br/>\n";
		echo "[i] URL: " . $changes['url'] . "<br/>\n";

		foreach($changes['update'] as $UID) {
			echo "[*] Updating object: $table $UID <br/>\n"; flush();

			//--------------------------------------------------------------------------------------
			//	download and add to database
			//--------------------------------------------------------------------------------------
			$rUrl = $peer->serverurl . 'sync/exprecord' . '/table_' . $table . '/' . $UID;
			echo '[*]' . $rUrl . "<br/>\n"; flush();	
			$recordxml = $sync->curlGet($rUrl, $sync->server['password']);

			if (strpos($recordxml, '</update>') != false) {
				//----------------------------------------------------------------------------------
				//	we have xml, base64_encoded copy of record
				//----------------------------------------------------------------------------------
				$objAry = $sync->base64DecodeSql($recordxml);
				$sync->dbSave($objAry['table'], $objAry['fields']);
				echo "adding object $UID to table $table <br/>\n"; flush();
				$kapenta->logSync("adding object $UID to table $table \n");

				//----------------------------------------------------------------------------------
				//	perform any deletions we've just become aware of
				//----------------------------------------------------------------------------------
				if ('revisions_deleted' == $table) {
					$refModel = $objAry['fields']['refModel'];
					$refUID = $objAry['fields']['refUID'];
					if (false == $revisions->isDeleted($refModel, $refUID)) {
						$sync->dbDelete($refModel, $refUID);
						echo "[*] Deleting object $refModel $refUID<br/>\n"; flush();
					} else {
						echo "[i] confirm $refModel $refUID (deleted)<br/>\n"; flush();
					}
				}

			} else {
				//----------------------------------------------------------------------------------
				//	record could not be downloaded
				//----------------------------------------------------------------------------------
				$peerUrl = $peer->serverurl;
				echo "[x] could not download $table $UID from $peerUrl <br/>\n"; flush();
				echo "<textarea rows='10' cols='80'>$recordxml</textarea><br/>\n"; 
				echo "<textarea rows='10' cols='80'>" . implode(file($rUrl)) . "</textarea><br/>\n";
				$kapenta->logSync("could not download $table $UID from $peerUrl \n");

			}

		}	// end foreach changed record

	}	// end foreach table

	//----------------------------------------------------------------------------------------------
	//	note the time on this peer
	//----------------------------------------------------------------------------------------------
	$peer->lastsync = time();
	$peer->save();

?>
