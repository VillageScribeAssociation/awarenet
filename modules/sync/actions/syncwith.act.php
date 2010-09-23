<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//-------------------------------------------------------------------------------------------------
//	perform a complete sync with another peer (check all tables and files)
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->doXmlError('only admins may do this'); }

	//---------------------------------------------------------------------------------------------
	//	load the server's record
	//---------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->doXmlError('peer not supplied'); }
	$peer = new Sync_Server($req->ref);
	if (false == $peer->loaded) { $page->doXmlError('peer not found'); }

	echo "<h1>Sync With: " . $peer->servername . "</h1>\n"; flush();
	//$syncIgnoreTables[] = 'comments';

	//---------------------------------------------------------------------------------------------
	//	sync database
	//---------------------------------------------------------------------------------------------
	$tables = $db->loadTables();
	foreach($tables as $table) {
		if (false == in_array($table, $sync->ignoreTables)) {
			$localSchema = $db->getSchema($table);
			//-------------------------------------------------------------------------------------
			//	get list of all records in this table
			//-------------------------------------------------------------------------------------
			echo "<h2>Checking: $table</h2>\n"; flush();
			$leUrl = $peer->serverurl . 'sync/tablele/' . str_replace('_', '-us-', $table);
			echo "at: $leUrl \n"; flush();
		
			$result = $sync->curlGet($leUrl, $peer->password);
			echo "loaded record list (" . strlen($result) . "bytes)<br/>\n"; flush();

			//-------------------------------------------------------------------------------------
			//	get all new records
			//-------------------------------------------------------------------------------------
			$lines = explode("\n", $result);
			foreach($lines as $line) {
				if (strpos($line, '|') != false) {
					$parts = explode('|', $line);
					$rUid = $parts[0];
					$rEditedBy = $parts[1];
					$rEditedOn = $parts[2];

					$import = true;

					echo "processing line: (uid: $rUid editedBy: $rEditedBy editedOn: $rEditedOn)";
					echo "<br/>\n"; flush();

					//-----------------------------------------------------------------------------
					//	check that we don't have a newer version than the peer
					//-----------------------------------------------------------------------------
					if (true == $db->objectExists($table, $rUid)) {
						$local = $db->load($rUid, $localSchema);
						if (strtotime($local['editedOn']) >= strtotime($rEditedOn)) { 
							$import = false; 
							echo "not updating record, local copy more recent<br/>\n"; flush();
						} else {
							echo "updating existing record, local copy out of date<br/>\n"; flush();
						}
					} else {
						echo "discovered new record<br/>\n"; flush();
					}

					//-----------------------------------------------------------------------------
					//	download and add to database
					//-----------------------------------------------------------------------------
					if (true == $import) {
						$rUrl = $peer->serverurl . 'sync/exprecord'
							  . '/table_' . $table . '/' . $rUid;

						echo $rUrl . "<br/>\n"; flush();

						$recordxml = $sync->curlGet($rUrl, $sync->server['password']);
					
						if (strpos($recordxml, '</update>') != false) {
							//---------------------------------------------------------------------
							//	we have xml, base64_encoded copy of record
							//---------------------------------------------------------------------
							$record = $sync->base64DecodeSql($recordxml);
							$sync->dbSave($record['model'], $record['fields']);
							echo "adding record $rUid to table $table <br/>\n"; flush();
							$kapenta->logSync("adding record $rUid to table $table \n");

						} else {
							//---------------------------------------------------------------------
							//	record could not be downloaded
							//---------------------------------------------------------------------
							$peerUrl = $peer->serverurl;
							echo "could not download $table $rUid from $peerUrl <br/>\n"; flush();
							echo "<textarea rows='10' cols='80'>$recordxml</textarea><br/>\n"; 
							echo "<textarea rows='10' cols='80'>" . implode(file($rUrl)) . "</textarea><br/>\n";
							$kapenta->logSync("could not download $table $rUid from $peerUrl \n");

						}
					} // end if (true == $import)	
				}
			}

		}
	}

	//---------------------------------------------------------------------------------------------
	//	sync files
	//---------------------------------------------------------------------------------------------


?>
