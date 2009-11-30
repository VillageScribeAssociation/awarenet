<?

//-------------------------------------------------------------------------------------------------
//	synchronize content and broadcast events between servers
//-------------------------------------------------------------------------------------------------
//	events, such as database updates or file stores are stored in the sync database which acts 
//	like a queue or outbox.  Once successfully passed to all peers the event is deleted.

require_once($installPath . 'modules/sync/models/sync.mod.php');
require_once($installPath . 'modules/sync/models/downloads.mod.php');

// 	tables which are not synced
$syncIgnoreTables = array(	'changes', 'sync', 'delitems', 'chat', 'downloads', 
							'servers', 'pageclients', 'pagechannels'  );	

//	notifications which are not synced
$syncIgnoreChannels = array('admin-syspagelog', 'admin-syspagelogsimple');	

//-------------------------------------------------------------------------------------------------
//	get this server's password (note: this should not be passed in any blocks, hence sql here)
//-------------------------------------------------------------------------------------------------

function syncGetOwnData() {
	$sql = "select * from servers where direction='self'";
	$result = dbQuery($sql);

	if (dbNumRows($result) == 1) {
		//-----------------------------------------------------------------------------------------
		//	exactly one record of self (ie, all is working out)
		//-----------------------------------------------------------------------------------------
		$row = dbFetchAssoc($result);
		$row = sqlRMArray($row);
		return $row;

	} else {
		//-----------------------------------------------------------------------------------------
		//	no record for self
		//-----------------------------------------------------------------------------------------
		if (dbNumRows($result) == 0) {
			$msg = "servers table does not contain a record for 'self'";
			logErr('sync', 'syncGetOwnPassword', $msg);
		}

		//-----------------------------------------------------------------------------------------
		//	ambiguity - more than one 'self' record
		//-----------------------------------------------------------------------------------------
		if (dbNumRows($result) > 1) {
			$msg = "servers table contains more than one record for 'self'";
			logErr('sync', 'syncGetOwnPassword', $msg);
		}	
		return false;
	}
}

//-------------------------------------------------------------------------------------------------
//	get this server's list of peers
//-------------------------------------------------------------------------------------------------

function syncListPeers() {
	$conditions = array("direction != 'self'");
	$peers = dbLoadRange('servers', '*', $conditions, '', '', '');
	return $peers;
}

//-------------------------------------------------------------------------------------------------
//	create a new sync event and fork a new thread/process to execute it
//-------------------------------------------------------------------------------------------------

function syncCreate($source, $type, $data) {
	global $installPath;
	global $serverPath;
	$uid = createUID();
	$peers = syncListPeers();
	
	logSync("syncCreate, source: $source type: $type data:\n$data\n)\n");

	foreach($peers as $peer) {
		if ($peer['serverurl'] != $source) {
			//-------------------------------------------------------------------------------------
			//	create record of data to be synced
			//-------------------------------------------------------------------------------------
			$model = new Sync();
			$model->data['UID'] = $uid;
			$model->data['source'] = $source;
			$model->data['type'] = $type;
			$model->data['data'] = $data;
			$model->data['peer'] = $peer['UID'];
			$model->data['status'] = 'new';
			$model->save();

			logSync("created sync item: $uid type: $type for: " . $peer['serverurl'] . "<br/>\n");

			//-------------------------------------------------------------------------------------
			//	fork off a new process to action this
			//-------------------------------------------------------------------------------------
			$model->data['UID'];
			$od = $installPath . 'data/temp/' . time() . '-' . $uid . '.sync';
			$url = $serverPath . 'sync/send/' . $uid;
			$shellCmd = "wget --output-document=" . $od . " $url";
			procExecBackground($shellCmd);

			logSync("executing sync item: $shellCmd <br/>\n");

		} else { logSync("not sending back to source $source\n"); } 
	}
}

//-------------------------------------------------------------------------------------------------
//	broadcast a database update to peers
//-------------------------------------------------------------------------------------------------

function syncBroadcastDbUpdate($source, $table, $record) {
	global $syncIgnoreTables;
	if (in_array($table, $syncIgnoreTables) == true) {
		logSync("ignoring database update, table: $table <br/>\n");
		return false;	
	}

	logSync("received database update, table: $table <br/>\n");
	$data = syncBase64EncodeSql($table, $record);
	return syncCreate($source, 'dbUpdate', $data);
}

//-------------------------------------------------------------------------------------------------
//	when a record is deleted
//-------------------------------------------------------------------------------------------------

function syncBroadcastDbDelete($source, $table, $UID) {
	global $syncIgnoreTables;
	if (in_array($table, $syncIgnoreTables) == true) {
		logSync("ignoring database update, table: $table <br/>\n");
		return false;	
	}

	logSync("received notice of record deleteion, table: $table uid: $UID <br/>\n");
	$data = "<deletion><table>$table</table><uid>$UID</uid></deletion>";
	return syncCreate($source, 'dbDelete', $data);	
}

//-------------------------------------------------------------------------------------------------
//	when a file is uploaded to downloaded into an awareNet server
//-------------------------------------------------------------------------------------------------

function syncBroadcastFileCreate($source, $fileName) {
	// NOT BROADCAST AT PRESENT, FILES ARE PULLED AS NEEDED
}

//-------------------------------------------------------------------------------------------------
//	when a file is deleted
//-------------------------------------------------------------------------------------------------

function syncBroadcastFileDelete($source, $fileName) {
	logSync("received notification of file deletion: $file<br/>\n");
	return syncCreate($source, 'fileDelete', $fileName);			
}

//-------------------------------------------------------------------------------------------------
//	broadcast a notification to page clients
//-------------------------------------------------------------------------------------------------

function syncBroadcastNotification($source, $channelID, $event, $data) {
	global $syncIgnoreChannels;
	logSync("received notification on channel: $channelID event: $event<br/>\n");
	if (in_array($channelID, $syncIgnoreChannels) == true) {
		logSync("ignoring notification, channel: $channelID <br/>\n");
		return false;	
	}

	$data = "<notification>\n"
		  . "\t<channelid>$channelID</channelid>\n"
		  . "\t<event>$event</event>\n"
		  . "\t<data>$data</data>\n"
		  . "</notification>\n";

	return syncCreate($source, 'notification', $data);	
}

//-------------------------------------------------------------------------------------------------
//	record that a record has been deleted
//-------------------------------------------------------------------------------------------------
//	so that is it not resurrected on sync with a server which still has this record

function syncRecordDeletion($refTable, $refUID) {
	global $syncIgnoreTables;
	if (in_array($refTable, $syncIgnoreTables) == true) { return false; }

	logSync("syncRecordDeletion: $refTable $refUID \n");

	//---------------------------------------------------------------------------------------------
	//	check if we already know that the record was deleted
	//---------------------------------------------------------------------------------------------
	if (syncRecordDeleted($refTable, $refUID) == true) { return false; }
	
	syncBroadcastDbDelete('self', $refTable, $refUID);

	//---------------------------------------------------------------------------------------------
	//	insert directly into database
	//---------------------------------------------------------------------------------------------
	$sql = "insert into delitems values ("
			 . "'" . createUID() . "', "
			 . "'" . sqlMarkup($refTable) . "', "
			 . "'" . sqlMarkup($refUID) . "', "
			 . "'" . time() . "')";	

	dbQuery($sql);
	return true;
}

//-------------------------------------------------------------------------------------------------
//	check if a record has been deleted
//-------------------------------------------------------------------------------------------------

function syncRecordDeleted($refTable, $refUID) {
	$sql = "select * from delitems "
		 . "where refUID='" . sqlMarkup($refUID) . "' "
		 . "and refTable='" . $refUID . "'";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) { return true; }
	return false;	
}

//-------------------------------------------------------------------------------------------------
//	get schema of sync table
//-------------------------------------------------------------------------------------------------
//	note that this also exists in /modules/sync/sync.mod.php, copy any changes there

function syncDbSchema() {
	$model = new Sync();
	return $model->initDbSchema();
}

//-------------------------------------------------------------------------------------------------
//	convert a record to base64_encoded XML
//-------------------------------------------------------------------------------------------------

function syncBase64EncodeSql($table, $data) {
	$xml = "<update>\n";
	$xml .= "\t<table>$table</table>\n";
	$xml .= "\t<fields>\n";
	foreach($data as $field => $value) {
		$xml .= "\t\t<" . $field . ">" . base64_encode($value) . "</" . $field . ">\n";
	}
	$xml .= "\t</fields>\n";
	$xml .= "</update>";

	logSync("generated:\n" . $xml . "\n");

	return $xml;
}

//-------------------------------------------------------------------------------------------------
//	convert base64_encoded XML to array
//-------------------------------------------------------------------------------------------------

function syncBase64DecodeSql($xml) {
	$data = array();
	$data['table'] = '';
	$data['fields'] = array();
	$xe = new XmlEntity($xml);

	logSync("RECIEVED: " . $xml);

	foreach ($xe->children as $child) {
		if ($child->type == 'table') { $data['table'] = $child->value; }
		if ($child->type == 'fields') {
			foreach($child->children as $field) { 
				$data['fields'][$field->type] = base64_decode($field->value); 
			}
		}
	}
	return $data;
}

//-------------------------------------------------------------------------------------------------
//	authenticate a request on the sync API
//-------------------------------------------------------------------------------------------------
//	HTTP header of request should contain a timestamp within 10 minutes of the request being
//	recieved and an authentication proof; sha1 hash of this server's password and the 
//	timestamp sha1('password' . 'timestamp')
//	Sync-timestamp: 12314235
//	Sync-proof:	69eba3375528ad645d6902792ca36132fc728d73
//	Sync-source: URL of peer

$syncTimeDiffMax = 600;		// 10 minutes

function syncAuthenticate() {
	$headers = syncGetHeaders();

	if (array_key_exists('Sync-Timestamp', $headers) == false) { return false; }
	if (array_key_exists('Sync-Proof', $headers) == false) { return false; }

	$tThen = $headers['Sync-Timestamp'];
	$tNow = time();

	//if (($tThen < ($tNow - $syncTimeDiffMax)) || ($tThen > ($tNow + $syncTimeDiffMax))) 
	//	{ return false; }	// timestamp too old or too far in future

	$myData = syncGetOwnData();
	$hash = sha1($myData['password'] . $tThen);

	//logSync("comparing hash: $hash to proof: " . $headers['Sync-Proof'] . "\n");

	if ($hash != $headers['Sync-Proof']) { return false; }

	// all seems in order
	logSync("Authenticated: $hash\n");
	return true;
}	

//-------------------------------------------------------------------------------------------------
//	get all HTTP headers (getallheaders() only works when PHP is apache module)
//-------------------------------------------------------------------------------------------------
//	source: http://www.rvaidya.com/blog/php/2009/02/25/get-request-headers-sent-by-client-in-php/

function syncGetHeaders() {
	//logSync("Getting request headers:\n");
    $headers = array();
    foreach ($_SERVER as $k => $v) {
        if (substr($k, 0, 5) == "HTTP_") {
            $k = str_replace('_', ' ', substr($k, 5));
            $k = str_replace(' ', '-', ucwords(strtolower($k)));
			if (substr($k, 0, 5) == 'Sync-') { $headers[$k] = $v; }
        }
    }
	//foreach($headers as $k => $v) { logSync("request header: $k - $v <br/>\n"); }
    return $headers;
}  

//-------------------------------------------------------------------------------------------------
//	save something to the database, without triggering sync or saving changes to record
//-------------------------------------------------------------------------------------------------

function syncDbSave($tableName, $data) {
	if (array_key_exists('UID', $data) == false) { return false; }	
	if (strlen(trim($data['UID'])) < 4) { return false; }

	logSync("saving record " . $data['UID'] . " to table $tableName \n");

	$dbSchema = dbGetSchema($tableName);

	//----------------------------------------------------------------------------------------------
	//	delete the current record
	//----------------------------------------------------------------------------------------------

	$sql = "delete from " . $dbSchema['table'] . " where UID='" . $data['UID'] . "'";
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	save a new one
	//----------------------------------------------------------------------------------------------

	$sql = "insert into " . $dbSchema['table'] . " values (";
	foreach ($dbSchema['fields'] as $fName => $fType) {
	  if (strlen($fName) > 0) {
		$quote = true;
		$value = ''; // . $fName . ':';

		//------------------------------------------------------------------------------------------
		//	some field types should be quotes, some not
		//------------------------------------------------------------------------------------------
		switch (strtolower($fType)) {
			case 'bigint': 		$quote = false; break;
			case 'tinyint';		$quote = false; break;
		}

		//------------------------------------------------------------------------------------------
		//	clean the value and add to array
		//------------------------------------------------------------------------------------------
		if (array_key_exists($fName, $data)) { $value = sqlMarkup($data[$fName]); } 
		if ($quote) { $value = "\"" . $value . "\""; }
		$sql .= $value . ',';
	   }
	}

	$sql = substr($sql, 0, strlen($sql) - 1);
	$sql .= ");";	
	dbQuery($sql);
}

//-------------------------------------------------------------------------------------------------
//	HTTP GET something from a peer 
//-------------------------------------------------------------------------------------------------

function syncCurlGet($url, $password) {
	global $proxyEnabled;
	global $proxyAddress;
	global $proxyPort;
	global $proxyUser;
	global $proxyPass;

	$ownData = syncGetOwnData();
	if (false == $ownData) { return false; }

	//---------------------------------------------------------------------------------------------
	//	load/create data about this peer
	//---------------------------------------------------------------------------------------------
	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($password . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	//---------------------------------------------------------------------------------------------
	//	create baisc cURL HTTP GET request
	//---------------------------------------------------------------------------------------------
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


	//---------------------------------------------------------------------------------------------
	//	use HTTP proxy if enabled
	//---------------------------------------------------------------------------------------------
	if ($proxyEnabled == 'yes') {
		$credentials = $proxyUser . ':' . $proxyPass;
		curl_setopt($ch, CURLOPT_PROXY, $proxyAddress);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		if (trim($credentials) != ':') {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
		}
	}

	//---------------------------------------------------------------------------------------------
	//	return result
	//---------------------------------------------------------------------------------------------
	$result = curl_exec($ch);
	return $result;
}

//-------------------------------------------------------------------------------------------------
//	HTTP GET something from a peer 
//-------------------------------------------------------------------------------------------------

function syncCurlPost($url, $password, $postVars) {
	global $proxyEnabled;
	global $proxyAddress;
	global $proxyPort;
	global $proxyUser;
	global $proxyPass;

	//---------------------------------------------------------------------------------------------
	//	load/create data about this peer
	//---------------------------------------------------------------------------------------------

	$ownData = syncGetOwnData();
	if (false == $ownData) { return false; }

	$syncTime = time();
	$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
	$syncProof = 'Sync-proof: ' . sha1($password . $syncTime);
	$syncSource = 'Sync-source: ' . $ownData['serverurl'];
	$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

	// squid does not completely support HTTP/1.1, see Expect: 100-continue in RFC 2616
	if ('yes' == $proxyEnabled) { $postHeaders[] = 'Expect:'; }	

	//---------------------------------------------------------------------------------------------
	//	set up basic cURL HTTP POST
	//---------------------------------------------------------------------------------------------

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//---------------------------------------------------------------------------------------------
	//	use HTTP proxy if enabled
	//---------------------------------------------------------------------------------------------

	if ('yes' == $proxyEnabled) {
		$credentials = $proxyUser . ':' . $proxyPass;
		curl_setopt($ch, CURLOPT_PROXY, $proxyAddress);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxyPort);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		if (trim($credentials) != ':') {
			curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
		}
	}

	//---------------------------------------------------------------------------------------------
	//	execute, and return result
	//---------------------------------------------------------------------------------------------
	$result = curl_exec($ch);
	return $result;
}

//-------------------------------------------------------------------------------------------------
//	request a file, search on peers and download it if found 
//-------------------------------------------------------------------------------------------------

function syncRequestFile($fileName) {
	global $installPath;
	global $serverPath;
	if (file_exists($installPath . $fileName) == true) { return false; }

	$model = new Download();
	if ($model->inList($fileName) == true) { return false; }	// already trying for this one

	$model->data['filename'] = $fileName;
	$model->data['status'] = 'wait';
	$model->data['timestamp'] = time();
	$model->save();

	logSync("syncRequestFile: $fileName download UID: " . $model->data['UID'] . " \n");

	if ($model->maxDownloads == false) {
		//-----------------------------------------------------------------------------------------
		//	the queue is not full, add it
		//-----------------------------------------------------------------------------------------
		logSync("creating new process for download: " . $model->data['UID'] . " \n");
		$od = $installPath . 'data/temp/' . createUID() . '.sync';
		$findUrl = $serverPath . 'sync/findfile/' . $model->data['UID'];
		$cmd = 'wget --output-document=' . $od . ' ' . $findUrl;
		procExecBackground($cmd);	
		return true;

	} else { logSync("server busy, queing download: " . $model->data['UID'] . " \n"); }
}

?>
