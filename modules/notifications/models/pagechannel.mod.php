<?

//--------------------------------------------------------------------------------------------------
//*	object for managing page notifications
//--------------------------------------------------------------------------------------------------
//+	A channel is a meeting point where pages can subscribe to be notified of events.  Channels
//+	consist of a list of subscribed clients (page instances by a UID).  When a notification
//+	is broadcast on a channel, the list of subscribed clients is loaded and the notification added 
//+	to all of their buffers (PageClients).  If any of these PageClients have timed out it is 
//+	removed from the channel.  If a channel has no subscribers it is removed.
//+
//+	NOTE:
//+	* PageChannels are created by client demand, they die without subscribers
//+	* clients is a simple list of pageUIDs delimited by newlines
//+	* channel IDs start with the name of the module which will be broadcasting, eg, admin-syslog
//+	* before subscribing to a channel, a user must pass authorization from the module

require_once($installPath . 'modules/notifications/models/pageclient.mod.php');

class PageChannel {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				// currently loaded record [array]
	var $dbSchema;			// database structure [array]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: channelID - ID of a page channel [string]

	function PageChannel($channelID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ('' != $channelID) { $this->load($channelID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a channel by ID, create a record for it if it does not exist
	//----------------------------------------------------------------------------------------------
	//arg: channelID - ID of a page channel [string]

	function load($channelID) {
		$sql = "select * from pagechannels where channelID='" . sqlMarkup($channelID) . "'";
		$result = dbQuery($sql);
		
		if (dbNumRows($result) == 0) {
			$this->data['UID'] = createUID();
			$this->data['channelID'] = $channelID;
			$this->data['clients'] = '';
			$this->save();
		} else {
			$this->data = sqlRMArray(dbFetchAssoc($result));
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//,	nothing to check as yet

	function verify() { return ''; }

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'pagechannels';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',	
			'channelID' => 'VARCHAR(130)',	
			'clients' => 'TEXT' );

		$dbSchema['indices'] = array('UID' => '10', 'channelID' => '20');
		$dbSchema['nodiff'] = array('UID', 'channelID', 'clients', 'timestamp');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	extended array of pagechannel members and metadata, unimplemented
	//----------------------------------------------------------------------------------------------

	function extArray() {
		// TODO
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.inc.php

	function install() {
		$report = "<h3>Installing PageChannels Table</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create notices table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('pagechannels') == false) {	
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created pagechannels table and indices...<br/>';
		} else {
			$this->report .= 'pagechannels table already exists...<br/>';	
		}

		return $report;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	delete the current record
	//----------------------------------------------------------------------------------------------

	function delete() {	
		dbDelete('pagechannels', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//.	check if a client is already subscribed to this channel
	//----------------------------------------------------------------------------------------------	
	//arg: pageUID - UID of a client page [string]
	//returns: true if this client is registered with the channel, otherwise false [bool]

	function hasClient($pageUID) { 
		return in_array($pageUID, explode("\n", $this->data['clients']));
	}

	//----------------------------------------------------------------------------------------------
	//.	subscribe a client to the channel
	//----------------------------------------------------------------------------------------------
	//arg: pageUID - UID of a client page [string]
	//returns: true on success, false on failure [bool]

	function addClient($pageUID) {
		//echo "#pagechannel->addClient('" . $pageUID . "');";
		if ($this->hasClient($pageUID) == false) {
			$this->data['clients'] = $this->data['clients'] . $pageUID . "\n";
			$this->save();
			return true;
		} else {
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a client from the channel
	//----------------------------------------------------------------------------------------------	
	//arg: pageUID - UID of a client page [string]

	function removeClient($pageUID) {
		//------------------------------------------------------------------------------------------
		//	remove the client/subscriber
		//------------------------------------------------------------------------------------------
		$clients = explode("\n", $this->data['clients']);
		$newClients = array();
		foreach($clients as $client) {
			if (($client != $pageUID) && (strlen($client) > 3)) { $newClients[] = $client; }
		}
		$this->data['clients'] = implode("\n", $newClients) . "\n";
		$this->save();

		//------------------------------------------------------------------------------------------
		//	remove this channel from the database if it has no subscribers
		//------------------------------------------------------------------------------------------
		if ('' == trim($newClients)) { $this->delete(); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	broadcast a message to all clients
	//----------------------------------------------------------------------------------------------	
	//arg: event - event type [string]
	//arg: data - details of the event [string]

	function broadcast($event, $data) {
		$clients = explode("\n", $this->data['clients']);
		foreach($clients as $client) {
			if (strlen(trim($client)) > 3) {
				//echo $this->data['channelID'] . " is broadcasting event " . $event . " to client $client<br/>\n"; 
				//echo "data: $data <br/>\n";
				$model = new PageClient($client);
				$model->addMessage($this->data['channelID'], $event, $data);
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a channel exists
	//----------------------------------------------------------------------------------------------	
	//arg: channelID - ID fo a page channel [string]
	//returns: true if a record exists for this channel, false if one does not [bool]

	function channelExists($channelID) {
		$sql = "select count(UID) as numInst from pagechannels where channelID='" . sqlMarkup($channelID) . "'";
		$result = dbQuery($sql);
		$row = dbFetchAssoc($result);
		if ($row['numInst'] == '0') { return false; }
		return true;
	}

}

?>
