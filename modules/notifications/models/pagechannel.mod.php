<?

//--------------------------------------------------------------------------------------------------
//	object for managing page notifications
//--------------------------------------------------------------------------------------------------
//	A channel is a meeting point where pages can subscribe to be notified of events.  Channels
//	consist of a list of subscribed clients (page instances by a UID).  When a notification
//	is broadcast on a channel, the list of subscribed clients is loaded and the notification added 
//	to all of their buffers (PageClients).  If any of these PageClients have timed out it is 
//	removed from the channel.  If a channel has no subscribers it is removed.
//
//	NOTE:
//	* PageChannels are created by client demand, they die without subscribers
//	* clients is a simple list of pageUIDs delimited by newlines
//	* channel IDs start with the name of the module which will be broadcasting, eg, admin-syslog
//	* before subscribing to a channel, a user must pass authorization from the module

require_once($installPath . 'modules/notifications/models/pageclient.mod.php');

class PageChannel {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				// currently loaded record
	var $dbSchema;			// database structure

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function PageChannel($channelID = '') {
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		if ('' != $channelID) { $this->load($channelID); }
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by userUID, create a notification queue if one does not exist
	//----------------------------------------------------------------------------------------------

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

	function loadArray($ary) { $this->data = $ary; }

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//	nothing to check as yet

	function verify() { return ''; }

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make array of notifications, ordered by time
	//----------------------------------------------------------------------------------------------

	function extArray() {
		// TODO
	}

	//----------------------------------------------------------------------------------------------
	//	install this module
	//----------------------------------------------------------------------------------------------

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
	//	delete a record
	//----------------------------------------------------------------------------------------------

	function delete() {	
		dbDelete('pagechannels', $this->data['UID']);
	}

	//----------------------------------------------------------------------------------------------
	//	check if a client is aldready subscribed to this channel
	//----------------------------------------------------------------------------------------------	

	function hasClient($pageUID) { 
		return in_array($pageUID, explode("\n", $this->data['clients']));
	}

	//----------------------------------------------------------------------------------------------
	//	subscribe a client to the channel
	//----------------------------------------------------------------------------------------------

	function addClient($pageUID) {
		echo "#pagechannel->addClient('" . $pageUID . "');";
		if ($this->hasClient($pageUID) == false) {
			$this->data['clients'] = $this->data['clients'] . $pageUID . "\n";
			$this->save();
			return true;
		} else {
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	remove a client from the channel
	//----------------------------------------------------------------------------------------------	

	function removeClient($pageUID) {
		//------------------------------------------------------------------------------------------
		//	remove the client/subscriber
		//------------------------------------------------------------------------------------------
		$clients = explode("\n", $this->data['clients']);
		$newClients = array();
		foreach($clients as $client) {
			if (($client != $pageUID) && (strlen($client) > 3)) { $newClients[] = $client; }
		}
		$this->data['clients'] = implode("\n", $newClients);
		$this->save();

		//------------------------------------------------------------------------------------------
		//	remove this channel from the database if it has no subscribers
		//------------------------------------------------------------------------------------------
		if ('' == trim($newClients)) { $this->delete(); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	broadcast a message to all clients
	//----------------------------------------------------------------------------------------------	

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
	//	discover if a channel exists
	//----------------------------------------------------------------------------------------------	

	function channelExists($channelID) {
		$sql = "select count(UID) as numInst from pagechannels where channelID='" . sqlMarkup($channelID) . "'";
		$result = dbQuery($sql);
		$row = dbFetchAssoc($result);
		if ($row['numInst'] == '0') { return false; }
		return true;
	}

}

?>
