<?

//-------------------------------------------------------------------------------------------------
//	perform a complete sync with another peer (check all tables and files)
//-------------------------------------------------------------------------------------------------

	if ($user->data['ofGroup'] != 'admin') { doXmlError('only admins may do this'); }
	require_once($installPath . 'modules/sync/models/servers.mod.php');

	//---------------------------------------------------------------------------------------------
	//	load the server's record
	//---------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { doXmlError('peer not supplied'); }
	if (dbRecordExists('servers', $request['ref']) == false) { doXmlError('peer not found'); }

	$peer = new Server($request['ref']);

	//---------------------------------------------------------------------------------------------
	//	sync database
	//---------------------------------------------------------------------------------------------
	$tables = dbListTables();
	foreach($tables as $table) {
		if (in_array($table, $syncIgnoreTables) == false) {
			//-------------------------------------------------------------------------------------
			//	get list of all records in this table
			//-------------------------------------------------------------------------------------
			echo "<h2>Checking: $table</h2>\n"; flush();
			$leUrl = $peer->data['serverurl'] . 'sync/tablele/' . $table;
			echo "at: $leUrl<br/>\n";
		
			$syncTime = time();
			$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
			$syncProof = 'Sync-proof: ' . sha1($server->data['password'] . $syncTime);
			$syncSource = 'Sync-source: ' . $ownData['serverurl'];
			$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

			$ch = curl_init($leUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);

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
					if (dbRecordExists($table, $rUid) == true) {
						$local = dbLoad($table, $rUid);
						if (strtotime($local['editedOn']) >= strtotime($rEditedOn)) { 
							$import = false; 
							echo "not updating record, local copy more recent<br/>\n";
						} else {
							echo "updating existing record, local copy out of date<br/>\n";
						}
					} else {
						echo "discovered new record<br/>\n";
					}

					//-----------------------------------------------------------------------------
					//	download and add to database
					//-----------------------------------------------------------------------------
					if (true == $import) {
						$rUrl = $peer->data['serverurl'] . 'sync/exprecord'
							  . '/table_' . $table . '/' . $rUid;

						echo $rUrl . "<br/>\n";

						$syncTime = time();
						$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
						$syncProof = 'Sync-proof: ' . sha1($server->data['password'] . $syncTime);
						$syncSource = 'Sync-source: ' . $ownData['serverurl'];
						$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

						$ch = curl_init($rUrl);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$recordxml = curl_exec($ch);
					
						if (strpos($recordxml, '</update>') != false) {
							//---------------------------------------------------------------------
							//	we have xml, base64_encoded copy of record
							//---------------------------------------------------------------------
							$record = syncBase64DecodeSql($recordxml);
							syncDbSave($record['table'], $record['fields']);
							echo "adding record $rUid to table $table <br/>\n";
							logSync("adding record $rUid to table $table \n");

						} else {
							//---------------------------------------------------------------------
							//	record could not be downloaded
							//---------------------------------------------------------------------
							$peerUrl = $peer->data['serverurl'];
							echo "could not download $table $rUid from $peerUrl <br/>\n";
							logSync("could not download $table $rUid from $peerUrl \n");

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
