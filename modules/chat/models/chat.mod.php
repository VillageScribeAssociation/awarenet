<?

//--------------------------------------------------------------------------------------------------
//*	object for managing chat queues
//--------------------------------------------------------------------------------------------------

//+ every user has a chat queue, a record to collect IMs sent to the user.  When the user is logged
//+ in the message pump will periodically check (and thus clear) the users chat queue, displaying
//+ messages via AJAX.
//+
//+	message structure:
//+
//+	<message>
//+    <UID>UID</UID>							// messages have small UIDs, like TX5Y2
//+    <from>UID</from>
//+	  <timestamp>12351253</timestamp>
//+    <content>this is a message.</content>
//+    <mine>yes|no</mine>						// yes for messages I sent, no for incoming
//+  </message>

class Chat {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;				// currently loaded record [array]
	var $dbSchema;			// database table structure [array]
	var $messages;			// expanded set of messages [array]
	var $queueSize = 500;	// maximum number of messages held in the queue [int]

	var $messageFields = 'UID|from|timestamp|content|mine'; // [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: userUID - UID of a user [string]

	function Chat($userUID = '') {
		global $user;
		$this->dbSchema = $this->initDbSchema();
		$this->data = dbBlank($this->dbSchema);
		$this->data['user'] = $user->data['UID'];
		if ($userUID != '') { $this->load($userUID); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record given user UID, create one if none exists
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID of a user record [string]
	//returns: true on success, false on failure [bool]

	function load($userUID) {
		$sql = "select * from chat where chat.user='" . sqlMarkup(trim($userUID)) . "'";
		$result = dbQuery($sql);
		while ($row = dbFetchAssoc($result)) {
			$this->data = sqlRMArray($row);	
			$this->expandMessages();
			return true;
		}
		// no chat queue, create one
		$this->data['UID'] = createUID();
		$this->data['user'] = sqlMarkup(trim($userUID));
		$this->messages = array();
		$this->data['queue'] = '';
		$this->save();
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
		$this->expandMessages();
	}

	//----------------------------------------------------------------------------------------------
	//.	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }
		$this->collapseMessages();
		dbSave($this->data, $this->dbSchema);
	}

	//----------------------------------------------------------------------------------------------
	//.	because its playing up
	//----------------------------------------------------------------------------------------------

	function update() {
		$sql = "update chat set queue='" . sqlMarkup($this->data['queue']) . "'"
			 . " where chat.user='" . sqlMarkup($this->data['user']) . "'";

		dbQuery($sql);
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() { 
		if (trim($this->data['queue']) == '') 
			{	$this->data['queue'] = "<chatqueue>\n</chatqueue>";	}

		// nothing to check at this stage
		return ''; 
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'chat';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',		
			'user' => 'VARCHAR(30)',
			'queue' => 'TEXT' );

		$dbSchema['indices'] = array('UID' => '10', 'user' => '10');
		$dbSchema['nodiff'] = array('UID', 'user', 'queue');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance  [array]

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of all data a view will need
	//----------------------------------------------------------------------------------------------
	//returns: extended array of member variables and metadata [array]

	function extArray() {
		$ary = $this->data;	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//.	install this module
	//----------------------------------------------------------------------------------------------
	//, deprecated, this should be handled by ../inc/install.inc.inc.php
	//returns: html report lines [string]

	function install() {
		$report = "<h3>Installing Chat Module</h3>\n";

		//------------------------------------------------------------------------------------------
		//	create book table if it does not exist
		//------------------------------------------------------------------------------------------

		if (dbTableExists('chat') == false) {	
			echo "installing chat module\n";
			dbCreateTable($this->dbSchema);	
			$this->report .= 'created chat table and indices...<br/>';
		} else {
			$this->report .= 'chat table already exists...<br/>';	
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand messages (XML -> array)
	//----------------------------------------------------------------------------------------------
	//returns: nested array of messages [array]

	function expandMessages() {
		$this->messages = array();
		if ($this->data['queue'] == '') { return false; }

		$messages = new XmlEntity($this->data['queue']);

		foreach($messages->children as $index => $child) {
			$newMsg = array();
			$newMsg['UID'] = $child->getFirst('UID');
			$newMsg['from'] = $child->getFirst('from');
			$newMsg['timestamp'] = $child->getFirst('timestamp');
			$newMsg['content'] = $child->getFirst('content');
			$newMsg['mine'] = $child->getFirst('mine');
			$this->messages[] = $newMsg;

		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse messages (Array -> XML) (returns true when the queue is full)
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function collapseMessages() {
		$count = 0;
		$this->data['queue'] = '';
		if (is_array($this->messages) == false) { return false; }
		$msgFields = explode('|', $this->messageFields);		

		$xe = new XmlEntity();
		$xe->type = 'chatqueue';

		foreach($this->messages as $m) {
			if (($count <= $this->queueSize) && ($m['UID'] != '')) {
				$mxe = new XmlEntity();
				$mxe->type = 'message';
				$mxe->isRoot = false;

				foreach($msgFields as $field) {	$mxe->addChild($field, $m[$field]);	}

				$xe->children[] = $mxe;

				$count++;
			}
		}

		$this->data['queue'] = $xe->toXml();
		return $true;
	}


	//----------------------------------------------------------------------------------------------
	//.	clear message queue
	//----------------------------------------------------------------------------------------------

	function clear() {
		$this->data['queue'] = '';
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//.	add a message
	//----------------------------------------------------------------------------------------------
	//arg: msgUID - message UID [string]
	//arg: fromUID - UID of user who sent this message [string]
	//arg: toUID - UID of recipient user [string]
	//arg: message - chat message [string]
	//arg: mine - whether the message was sent by the current user [string]
	//returns: true on success, false on failure

	function addMessage($msgUID, $fromUID, $toUID, $message, $mine) {
		if (trim($message) == '') { return false; }

		$newMessage = array(
			'UID' => $msgUID,		
			'from' => $fromUID,
			'timestamp' => time(),
			'content' => $message,
			'mine' => $mine	);

		$this->messages[$msgUID] = $newMessage;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a message UID
	//----------------------------------------------------------------------------------------------
	//returns: a new message UID [string]

	function createMsgUID() {
		$tempUID = "";
		for ($i = 0; $i < 5; $i++) { 
			$set = rand(1, 3);
			switch($set) {
				case 1:	$tempUID .= chr(rand(65, 90)); break;
				case 2:	$tempUID .= chr(rand(97, 122)); break;
				case 3:	$tempUID .= chr(rand(48, 57)); break;				
			}
		}
		return $tempUID;
	}

}

?>
