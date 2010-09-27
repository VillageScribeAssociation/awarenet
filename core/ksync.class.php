<?

	require_once($installPath . 'modules/sync/models/download.mod.php');

//-------------------------------------------------------------------------------------------------
//*	synchronize content and broadcast events between servers
//-------------------------------------------------------------------------------------------------
//+	events, such as database updates or file stores are stored in the sync database which acts 
//+	like a queue or outbox.  Once successfully passed to all peers the event is deleted.
//+ TODO: module config file

class KSync {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $ignoreTables;				//_	tables which are not synced [array]
	var $syncIgnoreChannels;		//_	notifications which are not synced [array]

	var $server;					//_	record of self, serialized Sync_Server [array]
	var $serverLoaded;				//_	set to true when server is loaded [bool]

	var $peers;						//_ range of serialized Sync_Server objects [array]
	var $peersLoaded;				//_	set to true when peers are loaded [bool]

	var $useLog = true;				//_	set totrue to enable sync logging [bool]

	var $timeDiffMax = 600;			// maximum age of sync messages on reciept (10 minutes) [int]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KSync() {
		global $kapenta;		

		if ('yes' == $kapenta->proxyEnabled) { $this->proxyEnabled = true; }
		$this->proxyAddress = $kapenta->proxyAddress;
		$this->proxyPort = $kapenta->proxyPort;
		$this->proxyUser = $kapenta->proxyUser;
		$this->proxyPass = $kapenta->proxyPass;

		$this->ignoreTables = array(
			'Sync_Notices',
			'Deleted',
			'chat',
			'download',
			'Sync_Download',
			'Sync_Message',
			'Sync_Servers'
		);

		$this->ignoreChannels = array('admin-syspagelog', 'admin-syspagelogsimple');	

		$this->peers = array();
		$this->loadPeers();
	}

	//----------------------------------------------------------------------------------------------
	//.	get this server's password (note: this should not be passed in any blocks, hence sql here)
	//----------------------------------------------------------------------------------------------
	//returns: peer server record (database makeup removed) or false if not found [array] [bool]

	function getOwnData() {
		if (false == $this->peersLoaded) { $this->loadPeers(); }
		if (true == $this->serverLoaded) { return $this->server; }
		return false;
	}

	//-------------------------------------------------------------------------------------------------
	//.	get this server's list of (active) peers
	//-------------------------------------------------------------------------------------------------
	//returns: array of serialized Sync_Server objects, or false on failure [array][bool]

	function loadPeers() {
		global $db;
		$peers = array();
		$conditions = array("active='active'");	

		$range = $db->loadRange('servers', '*', $conditions, '', '', '');
		if (false == $range) { return false; }

		foreach($range as $peer) {
			if ('self' != $peer['direction']) { $this->peers[] = $peer; }
			else {
				$this->server = $peer;
				$this->serverLoaded = true;
			}
		}

		$this->peersLoaded = true;
		return $peers;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a new sync event and fork a new thread/process to execute it
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: type - type (dbUpdate|dbDelete|fileCreate|fileDelete|notification) [string]
	//arg: data - message data, often base64 encoded XML [string]

	function create($source, $type, $data) {
		global $kapenta;

		$uid = $kapenta->createUID();
	
		if (true == $this->useLog) 
			{ logSync("syncCreate, source: $source type: $type data:\n$data\n)\n");	}

		foreach($this->peers as $peer) {
			if ($peer['serverurl'] != $source) {
				//----------------------------------------------------------------------------------
				//	create record of data to be synced
				//----------------------------------------------------------------------------------
				$model = new Sync_Notice();
				$model->UID = $uid;
				$model->source = $source;
				$model->type = $type;
				$model->data = $data;
				$model->peer = $peer['UID'];
				$model->status = 'new';
				$model->save();

				if (true == $this->useLog) {
					$msg = "created sync item: $uid type: $type for: " . $peer['serverurl'] . "<br/>\n";
					logSync($msg);
				}

				//----------------------------------------------------------------------------------
				//	fork off a new process to action this
				//----------------------------------------------------------------------------------
				$model->UID;
				$od = $installPath . 'data/temp/' . time() . '-' . $uid . '.sync';
				$url = $serverPath . 'sync/send/' . $uid;
				$shellCmd = "wget --output-document=" . $od . " $url";
				$kapenta->procExecBackground($shellCmd);

				if (true == $this->useLog) { logSync("executing sync item: $shellCmd <br/>\n"); }

				} else { 
					if (true == $this->useLog) { logSync("not sending back to source $source\n"); }
				} 
			}
		}

	//----------------------------------------------------------------------------------------------
	//.	broadcast a database update to peers
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: table - name of table containing record to be updated [string]
	//arg: record - XML with base64 encoded values [string]

	function broadcastDbUpdate($source, $table, $record) {
		if (true == in_array($table, $this->ignoreTables)) {
			if (true == $this->useLog)
				{ logSync("ignoring database update, table: $table <br/>\n"); }
			return false;
		}

		if (true == $this->useLog) { logSync("received database update, table: $table <br/>\n"); }
		$data = $this->base64EncodeSql($table, $record);
		return $this->create($source, 'dbUpdate', $data);
	}

	//----------------------------------------------------------------------------------------------
	//.	when an object is deleted
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: table - name of database table this record belongs to [string]
	//arg: UID - UID of the record being deleted

	function broadcastDbDelete($source, $table, $UID) {
		if (in_array($table, $this->ignoreTables) == true) {
			if (true == $this->useLog) 
				{ logSync("ignoring database update, table: $table <br/>\n"); }
			return false;	
		}

		if (true == $this->useLog) 
			{ logSync("received notice of record deleteion, table: $table uid: $UID <br/>\n"); }

		$data = "<deletion><table>$table</table><uid>$UID</uid></deletion>";
		return $this->create($source, 'dbDelete', $data);	
	}	

	//----------------------------------------------------------------------------------------------
	//.	when a file is uploaded to downloaded into an awareNet server
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: fileName - relative to installPath [string]
	//: not implemented here (files are pulled as needed, implement if you want a pushing system)

	function broadcastFileCreate($source, $fileName) {
		// NOT BROADCAST AT PRESENT, FILES ARE PULLED AS NEEDED
	}

	//----------------------------------------------------------------------------------------------
	//.	when a file is deleted
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: fileName - relative to installPath [string]

	function broadcastFileDelete($source, $fileName) {
		if (true == $this->useLog) 
			{ logSync("received notification of file deletion: $file<br/>\n"); }
		return $this->create($source, 'fileDelete', $fileName);			
	}

	/*
	//----------------------------------------------------------------------------------------------
	//.	broadcast a notification to page clients DEPRECATED
	//----------------------------------------------------------------------------------------------
	//arg: source - source of this message [string]
	//arg: channelID - label identifying a page channel [string]
	//arg: event - as defined by channel [string]
	//arg: data - usually a base64 encoded string [string]

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
	*/

	/*	TODO: replace with either global revisions object, or method on $kapenta
	//----------------------------------------------------------------------------------------------
	//|	note that a record has been deleted
	//----------------------------------------------------------------------------------------------
	//: this is so that is it not resurrected on sync with a server which still has this record
	//arg: refTable - table which contained the deleted record [string]
	//arg: refUID - UID of the deleted record [string]
	//opt: source - peer we heard about this deletion from [string]
	//returns: true on success [bool]

	function syncRecordDeletion($refTable, $refUID, $source = 'self') {
		global $syncIgnoreTables, $kapenta;
		if (in_array($refTable, $syncIgnoreTables) == true) { return false; }

		logSync("syncRecordDeletion: $refTable $refUID \n");

		//------------------------------------------------------------------------------------------
		//	check if we already know that the record was deleted
		//------------------------------------------------------------------------------------------
		if (syncRecordDeleted($refTable, $refUID) == true) { return false; }
	
		syncBroadcastDbDelete($source, $refTable, $refUID);

		//------------------------------------------------------------------------------------------
		//	insert directly into database
		//------------------------------------------------------------------------------------------
		$sql = "insert into delitems values ("
				 . "'" . $kapenta->createUID() . "', "
				 . "'" . sqlMarkup($refTable) . "', "
				 . "'" . sqlMarkup($refUID) . "', "
				 . "'" . time() . "')";	

		$db->query($sql);
		return true;
	}
	*/

	/*	TODO: replace with $revisions object, or a method on $kapenta
	//----------------------------------------------------------------------------------------------
	//.	check if a record has been deleted
	//----------------------------------------------------------------------------------------------
	//arg: refTable - table which contained(s) deleted record [string]
	//arg: refUID - UID of deleted record [string]
	//returns: [bool]

	function syncRecordDeleted($refTable, $refUID) {
		$sql = "select * from delitems "
			 . "where refUID='" . sqlMarkup($refUID) . "' "
			 . "and refTable='" . $refUID . "'";

		$result = $db->query($sql);
		if (dbNumRows($result) > 0) { return true; }
		return false;	
	}
	*/

	//----------------------------------------------------------------------------------------------
	//.	get schema of sync table
	//----------------------------------------------------------------------------------------------
	//:	note that this also exists in /modules/sync/sync.mod.php, copy any changes there
	//returns: dbSchema, see mysql.inc.php [array]

	function getDbSchema() {
		$model = new Sync_Notice();
		$dbSchema = $model->getDbSchema();
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert a record to base64_encoded XML
	//----------------------------------------------------------------------------------------------
	//arg: table - name of database table [string]
	//arg: data - associative array of field name => value pairs [array]
	//returns: base64 encoded XML [string]

	function base64EncodeSql($table, $data) {
		global $kapenta;
		$xml = "<update>\n";
		$xml .= "\t<table>$table</table>\n";
		$xml .= "\t<fields>\n";
		foreach($data as $field => $value) {
			$xml .= "\t\t<" . $field . ">" . base64_encode($value) . "</" . $field . ">\n";
		}
		$xml .= "\t</fields>\n";
		$xml .= "</update>";

		if (true == $this->useLog) { $kapenta->logSync("generated:\n" . $xml . "\n"); }
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert base64_encoded XML to array
	//----------------------------------------------------------------------------------------------
	//arg: xml - base64 encoded XML representation of a record [string]
	//returns: partial dbSchema [array]

	function base64DecodeSql($xml) {
		global $kapenta;
		$data = array();				//% return value [array]
		$data['model'] = '';
		$data['fields'] = array();

		$doc = new KXmlDocument($xml);
		if (true == $this->useLog) { $kapenta->logSync("RECIEVED: " . $xml); }

		$children = $doc->getChildren(1);	// get children of root node [array]
		foreach($children as $childId) {
			$entity = $doc->getEntity($childId);
			if ('table' == $entity['type']) { $data['model'] = $entity['value']; }
			if ('fields' == $entity['type']) {
				$fields = $doc->getChildren($childId);	// children of 'fields' entity
				foreach($fields as $fieldId) {
					$fent = $doc->getEntity($fieldId);
					$data['fields'][$fent['type']] = base64_decode($fent['value']);
				}
			}
		}
		return $data;
	}

	//----------------------------------------------------------------------------------------------
	//.	authenticate a request on the sync API
	//----------------------------------------------------------------------------------------------
	//:	HTTP header of request should contain a timestamp within 10 minutes of the request being
	//:	recieved and an authentication proof; sha1 hash of this server's password and the 
	//:	timestamp sha1('password' . 'timestamp')
	//:	Sync-timestamp: 12314235
	//:	Sync-proof:	69eba3375528ad645d6902792ca36132fc728d73
	//:	Sync-source: URL of peer
	//returns: true if credentials sent in header by current browser request check out [bool]

	function authenticate() {
		$headers = $this->getHeaders();

		if (array_key_exists('Sync-Timestamp', $headers) == false) { return false; }
		if (array_key_exists('Sync-Proof', $headers) == false) { return false; }

		$tThen = $headers['Sync-Timestamp'];
		$tNow = time();

		//if (($tThen < ($tNow - $this->timeDiffMax)) || ($tThen > ($tNow + $this->timeDiffMax))) 
		//	{ return false; }	// timestamp too old or too far in future

		$hash = sha1($this->server['password'] . $tThen);

		if ($hash != $headers['Sync-Proof']) { return false; }

		// all seems in order
		if (true == $this->useLog) { logSync("Authenticated: $hash\n"); }
		return true;
	}	

	//----------------------------------------------------------------------------------------------
	//.	get all HTTP headers (getallheaders() only works when PHP is apache module)
	//----------------------------------------------------------------------------------------------

	//+	source: http://www.rvaidya.com/blog/php/2009/02/25/get-request-headers-sent-by-client-in-php/
	//returns: the headers [array]

	function getHeaders() {
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

	//----------------------------------------------------------------------------------------------
	//.	save something to the database, without triggering sync or saving changes to record
	//----------------------------------------------------------------------------------------------
	//arg: tableName - name of a database table [string]
	//arg: data - array fo field name => value pairs [array]
	//returns: true on success, false on failure [bool]
	//: TODO: maybe move this into mysql.inc.php
	//: TODO: do easy on indices and use SQL update rather than delete/insert

	function dbSave($tableName, $data) {
		global $kapenta, $db;
		if (false == is_array($data)) {
			echo "Invalid data sent to \$sync->dbSave()<br/>\n"; 
			return false; 
		}
		if (false == array_key_exists('UID', $data)) { return false; }	
		if (strlen(trim($data['UID'])) < 4) { return false; }

		if (true == $this->useLog) 
			{ $kapenta->logSync("saving record " . $data['UID'] . " to table $tableName \n"); }

		$dbSchema = $db->getSchema($tableName);

		$db->save($data, $dbSchema, false);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	HTTP GET something from a peer 
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of sync API on peer [string]
	//arg: password - password for this peer [string]
	//returns: result of HTTP request [string]
	//TODO: move this to user

	function curlGet($url, $password) {
		//TODO: use these as member variables taken from $kapenta
		global $hostInterface;
		global $proxyEnabled;
		global $proxyAddress;
		global $proxyPort;
		global $proxyUser;
		global $proxyPass;

		$ownData = $this->getOwnData();
		if (false == $ownData) { return false; }

		//------------------------------------------------------------------------------------------
		//	load/create data about this peer
		//------------------------------------------------------------------------------------------
		$syncTime = time();
		$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
		$syncProof = 'Sync-proof: ' . sha1($password . $syncTime);
		$syncSource = 'Sync-source: ' . $ownData['serverurl'];
		$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

		//------------------------------------------------------------------------------------------
		//	create baisc cURL HTTP GET request
		//------------------------------------------------------------------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ('' != $hostInterface) { curl_setopt($ch, CURLOPT_INTERFACE, $hostInterface); }

		//------------------------------------------------------------------------------------------
		//	use HTTP proxy if enabled
		//------------------------------------------------------------------------------------------
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

		//------------------------------------------------------------------------------------------
		//	return result
		//------------------------------------------------------------------------------------------
		$result = curl_exec($ch);
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//.	HTTP POST something to a peer 
	//----------------------------------------------------------------------------------------------
	//arg: url - URL of sync API on peer [string]
	//arg: password - password for this peer [string]
	//arg: postVars - body of post [array]
	//returns: result of HTTP POST, as returned by peer [string]
	//TODO: move this to utils

	function curlPost($url, $password, $postVars) {
		global $kapenta;
		global $hostInterface;

		//------------------------------------------------------------------------------------------
		//	load/create data about this peer
		//------------------------------------------------------------------------------------------
		$ownData = $this->getOwnData();
		if (false == $ownData) { return false; }

		$syncTime = time();
		$syncTimestamp = 'Sync-timestamp: ' . $syncTime;
		$syncProof = 'Sync-proof: ' . sha1($password . $syncTime);
		$syncSource = 'Sync-source: ' . $ownData['serverurl'];
		$postHeaders = array($syncTimestamp, $syncProof, $syncSource);

		// squid does not completely support HTTP/1.1, see Expect: 100-continue in RFC 2616
		if ('yes' == $kapenta->proxyEnabled) { $postHeaders[] = 'Expect:'; }	

		//------------------------------------------------------------------------------------------
		//	set up basic cURL HTTP POST
		//------------------------------------------------------------------------------------------
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $postHeaders);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if ('' != $hostInterface) { curl_setopt($ch, CURLOPT_INTERFACE, $hostInterface); }

		//------------------------------------------------------------------------------------------
		//	use HTTP proxy if enabled
		//------------------------------------------------------------------------------------------
		if ('yes' == $kapenta->proxyEnabled) {
			$credentials = $kapenta->proxyUser . ':' . $kapenta->proxyPass;
			curl_setopt($ch, CURLOPT_PROXY, $kapenta->proxyAddress);
			curl_setopt($ch, CURLOPT_PROXYPORT, $kapenta->proxyPort);
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			if (trim($credentials) != ':') {
				curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
			}
		}

		//------------------------------------------------------------------------------------------
		//	execute, and return result
		//------------------------------------------------------------------------------------------
		$result = curl_exec($ch);
		return $result;
	}

	//----------------------------------------------------------------------------------------------
	//	request a file, search on peers and download it if found 
	//----------------------------------------------------------------------------------------------
	//arg: fileName - relative to installPath [string]
	//returns: true if requested, distinct from request being filled [bool]

	function requestFile($fileName) {
		global $kapenta;
		if (true == $kapenta->fileExists($fileName)) { return false; }

		$model = new Sync_Download();
		if (true == $model->inList($fileName)) { return false; }	// already trying for this one

		$model->filename = $fileName;
		$model->status = 'wait';
		$model->timestamp = time();
		$model->save();

		if (true == $this->useLog) 
			{ logSync("syncRequestFile: $fileName download UID: " . $model->UID . " \n"); }

		if (false == $model->maxDownloads) {
			//--------------------------------------------------------------------------------------
			//	the queue is not full, add it
			//--------------------------------------------------------------------------------------
			if (true == $this->useLog) 
				{ logSync("creating new process for download: " . $model->UID . " \n"); }

			$od = $kapenta->installPath . 'data/temp/' . $kapenta->createUID() . '.sync';
			$findUrl = $serverPath . 'sync/findfile/' . $model->UID;
			$cmd = 'wget --output-document=' . $od . ' ' . $findUrl;
			$kapenta->procExecBackground($cmd);

		} else { 
			if ($this->useLog) { logSync("server busy, queing download: " . $model->UID . " \n"); }
		}
		return true;
	}

}

?>
