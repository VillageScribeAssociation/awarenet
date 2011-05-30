<?

	require_once($kapenta->installPath . 'modules/sync/models/server.mod.php');

//--------------------------------------------------------------------------------------------------
//	utility functions for performing periodic sync operations
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	sync all database tables
//--------------------------------------------------------------------------------------------------
//arg: serverUID - UID of a Sync_Server object [string]
//returns: html report of sync operations [string]

function sync_entireDatabase($serverUID) {
	global $kapenta, $db, $sync, $revisions;
	$report = '';		//%	html [string]

	//----------------------------------------------------------------------------------------------
	//	load the server's record
	//----------------------------------------------------------------------------------------------
	$peer = new Sync_Server($serverUID);
	if (false == $peer->loaded) { return 'peer not found'; }
	$report .= "<h1>Sync With: " . $peer->servername . "</h1>\n";

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
		$report .= "<h2>Checking: $table</h2>\n"; flush();

		$changes = $sync->getTableLe($peer->UID, $table);

		$report .= "[i] Total Objects: " . $changes['total'] . "<br/>\n";
		$report .= "[i] Dirty Objects: " . $changes['dirty'] . "<br/>\n";
		$report .= "[i] URL: " . $changes['url'] . "<br/>\n";

		foreach($changes['update'] as $UID) {
			$report .= "[*] Updating object: $table $UID <br/>\n"; flush();

			//--------------------------------------------------------------------------------------
			//	download and add to database
			//--------------------------------------------------------------------------------------
			$rUrl = $peer->serverurl . 'sync/exprecord' . '/table_' . $table . '/' . $UID;
			$report .= '[*]' . $rUrl . "<br/>\n"; flush();	
			$recordxml = $sync->curlGet($rUrl, $sync->server['password']);

			if (strpos($recordxml, '</update>') != false) {
				//----------------------------------------------------------------------------------
				//	we have xml, base64_encoded copy of record
				//----------------------------------------------------------------------------------
				$objAry = $sync->base64DecodeSql($recordxml);
				$sync->dbSave($objAry['table'], $objAry['fields']);
				$report .= "adding object $UID to table $table <br/>\n";
				$kapenta->logSync("adding object $UID to table $table \n");

				//----------------------------------------------------------------------------------
				//	perform any deletions we've just become aware of
				//----------------------------------------------------------------------------------
				if ('revisions_deleted' == $table) {
					$refModel = $objAry['fields']['refModel'];
					$refUID = $objAry['fields']['refUID'];
					if (false == $revisions->isDeleted($refModel, $refUID)) {
						$sync->dbDelete($refModel, $refUID);
						$report .= "[*] Deleting object $refModel $refUID<br/>\n";
					} else {
						$report .= "[i] confirm $refModel $refUID (deleted)<br/>\n";
					}
				}

			} else {
				//----------------------------------------------------------------------------------
				//	record could not be downloaded
				//----------------------------------------------------------------------------------
				$peerUrl = $peer->serverurl;
				$report .= "[x] could not download $table $UID from $peerUrl <br/>\n"; flush();
				$report .= "<textarea rows='10' cols='80'>$recordxml</textarea><br/>\n"; 
				$report .= "<textarea rows='10' cols='80'>" . implode(file($rUrl)) . "</textarea><br/>\n";
				$kapenta->logSync("could not download $table $UID from $peerUrl \n");

			}

		}	// end foreach changed record

	}	// end foreach table

	//----------------------------------------------------------------------------------------------
	//	note the time on this peer
	//----------------------------------------------------------------------------------------------
	$peer->lastsync = time();
	$peer->save();

	return $report;
}


//--------------------------------------------------------------------------------------------------
//|	sync all user files
//--------------------------------------------------------------------------------------------------
//arg: serverUID - UID of a Sync_Server object [string]
//returns: html report of sync operations [string]

function sync_allFiles($serverUID) {
	global $kapenta, $db, $sync;
	$report = '';		//%	return value [string]

	//---------------------------------------------------------------------------------------------
	//	check reference and admin permissions
	//---------------------------------------------------------------------------------------------
	if ('' == $serverUID) { return 'Peer UID not given.'; }

	$peer = new Sync_Server($serverUID);
	if (false == $peer->loaded) { return 'No such peer'; }

	//---------------------------------------------------------------------------------------------
	//	get a list of all files from the images module and add to download queue
	//---------------------------------------------------------------------------------------------
	$url = $peer->serverurl . 'sync/listfiles/format_csv/';
	$report .= "[*] Downloading list: $url<br/>\n";

	$raw = $sync->curlGet($url, $peer->password);
	$report .= "[i] " . strlen($raw) . " bytes<br/>\n";
	$lines = explode("\n", $raw);
	foreach($lines as $line) {
		$parts = explode(",", $line, 4);
		if (4 == count($parts)) {
			$refModule = trim($parts[0]);
			$refModel = trim($parts[1]);
			$refUID = trim($parts[2]);
			$fileName = trim($parts[3]);

			$report .= "[>] $refModule - $refModel - $refUID :: $fileName <br/>\n";

			if ( (trim($fileName) != '') && (false == $kapenta->fileExists($fileName)) ) {
				//----------------------------------------------------------------------------------
				// filename is valid and does not exist on this server
				//----------------------------------------------------------------------------------
				//$sync->requestFile($fileName);
				$fileUrl = $peer->serverurl . $fileName;
				echo "downloading: $fileUrl<br/>\n";
				$raw = $sync->curlGet($fileUrl, $peer->password);
				$report .= "[*] Downloaded: " . $fileUrl . " (" . strlen($raw) . " bytes)<br/>\n";
				$allOk = true;

				if ('' == $raw) { $allOk = false; }
				//if ((true == $allOk) && (false === @imagecreatefromstring($raw))) { $allOk = false; }

				if (true == $allOk) {
					$kapenta->filePutContents($fileName, $raw, true);
					$report .= "[i] Saved $fileName <br/>\n";
				}

			} else { 
				$report .= "[*] Exists: " . $fileName . "<br/>\n";
			}
		}
	}

	return $report;
}

?>
