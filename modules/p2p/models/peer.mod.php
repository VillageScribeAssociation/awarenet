<?

	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Math/BigInteger.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/Random.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/Hash.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/RSA.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/AES.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/bzutils.inc.php');

//--------------------------------------------------------------------------------------------------
//*	Record of another kapenta instance which we trust.
//--------------------------------------------------------------------------------------------------

class P2P_Peer {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ title [string]
	var $url;				//_ varchar(255) [string]
	var $firewalled;		//_ is this peer behind a firewall? (yes|) varchar(10) [string]
	var $pubkey;			//_ plaintext [string]
	var $status;			//_ varchar(30) [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared = 'no';		//_ varchar(10) [string]

	var $secure = false;	//_	set to true if both peers support encryption [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Peer object [string]

	function P2P_Peer($UID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }		// try load an object from the database
		if (false == $this->loaded) {				// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Peer object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Peer object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		global $kapenta;
		if (false == is_array($ary)) { return false; }

		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->url = $ary['url'];
		$this->firewalled = $ary['firewalled'];
		$this->pubkey = $ary['pubkey'];
		$this->status = $ary['status'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = 'no';

		$this->secure = true;

		if (
			('plaintext' == $this->pubkey) ||
			('plaintext' == $kapenta->registry->get('p2p.server.pubkey'))
		) {
			$this->secure = false;		//	fallback to plaintext transport
		}

		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		global $aliases;

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error:<br/>\n" . $kapenta->db->lasterr; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'p2p';
		$dbSchema['model'] = 'p2p_peer';
		$dbSchema['archive'] = 'no';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'url' => 'VARCHAR(255)',
			'firewalled' => 'VARCHAR(10)',
			'pubkey' => 'TEXT',
			'status' => 'VARCHAR(30)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(10)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'name' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'name',
			'url',
			'firewalled',
			'pubkey',
			'shared',
			'status'
		);

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'name' => $this->name,
			'url' => $this->url,
			'firewalled' => $this->firewalled,
			'pubkey' => $this->pubkey,
			'status' => $this->status,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';
		$ext['testUrl'] = '';	$ext['testLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('p2p', 'p2p_peer', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%p2p/showpeer/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_peer', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%p2p/editpeer/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";

			$ext['testUrl'] = '%%serverPath%%p2p/testsend/' . $ext['UID'];
			$ext['testLink'] = "<a href='" . $ext['testUrl'] . "'>[ test ]</a>";

			$ext['scanUrl'] = '%%serverPath%%p2p/findgifts/' . $ext['UID'];
			$ext['scanLink'] = "<a href='" . $ext['scanUrl'] . "'>[ scan for gifts ]</a>";
		}

		if (true == $user->authHas('p2p', 'p2p_peer', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%p2p/confirmdelete/UID_' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $kapenta->db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $kapenta->db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}
	
	//==============================================================================================
	//	network IO
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	send a message to this peer
	//----------------------------------------------------------------------------------------------
	//returns: empty string on success, error message on failure [string]

	function sendMessage($action, $message) {
		global $kapenta;
		global $utils;

		$url = $this->url . 'p2p/' . $action . '/';				//%	interface to POST to [string]
		$report = ''; 											//%	return value [string]
		$signature = '';										//%	against own prv key [string]
		$prvkey = $kapenta->registry->get('p2p.server.prvkey');			//%	this peer's signing key [string] 

		if ('' == $prvkey) { return 'No private key set in registry.'; }

		$prvkeyid = openssl_get_privatekey($prvkey);

		openssl_sign($message, $signature, $prvkeyid);		// compute using OPENSSL_ALGO_SHA1
		openssl_free_key($prvkeyid);						// free the key

		$postvars = array(
			'peer' => $kapenta->registry->get('p2p.server.uid'),
			'message' => base64_encode($message),
			'signature' => base64_encode($signature)
		);

		$returns = $utils->curlPost($url, $postvars);
		
		if ('<ok/>' != $returns) { $report .= $returns; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	check a message sent by this peer, ensure signature matches data
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [string]

	function checkMessage($message, $signature) {
		$pubkeyid = openssl_get_publickey($this->pubkey);
		$check = openssl_verify($message, $signature, $pubkeyid);
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	try to download updates from this peer
	//----------------------------------------------------------------------------------------------
	//returns: raw update list [string]

	function getUpdates() {
		global $utils;
		global $kapenta;

		if (false == $this->loaded) { return "## Peer not loaded\n"; }
		
		$ownUID = $kapenta->registry->get('p2p.server.uid');
		if ('' == $ownUID) { return '<error>Server UID not set in registry.</error>'; }

		$url = $this->url . 'p2p/updatesfor/' . $ownUID;
		$raw = $utils->curlGet($url);

		if ('queue empty' == $raw) { return array(); }

		$msg = $this->unpack($raw);

		return $msg;
	}

	//----------------------------------------------------------------------------------------------
	//.	confirm reciept fo a set of updates
	//----------------------------------------------------------------------------------------------

	function ackUpdates($fileName) {
		global $utils;
		$fileName = basename($fileName);
		$url = $this->url . 'p2p/confirm/' . $fileName;
		$raw = $utils->curlGet($url);
		return $raw;
	}

	//==============================================================================================
	//	P2P Encryption routines
	//==============================================================================================
	//+	current version of the protocol uses 1024 bit RSA keys to encrypt a per-message AES key.
	//+	the same RSA keypair is used for encryption and signing.

	//----------------------------------------------------------------------------------------------
	//.	encrypt a message for this peer (using peer's public key)
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to be encrypted [string]	

	function encrypt($plaintext) {
		global $kapenta;

		$packet = '';												//%	return value [string]
		$aeskey = $kapenta->createUID() . $kapenta->createUID();	//%	random key [string]

		//	encrypt plaintext with AES (random key)
		$aes = new Crypt_AES();
		$aes->setKey($aeskey);
		$aes_ct = $aes->encrypt($plaintext);

		//	encrypt the AES key with this peer's RSA public key
		$rsa = new Crypt_RSA();
		$rsa->loadKey($this->pubkey);
		$rsa_ct = $rsa->encrypt($aeskey);

		//	compose into packet, base64 encoding prevents some issues with HTTP, inefficient
		$packet = mb_strlen($rsa_ct, 'ASCII') . '|' . $rsa_ct . '|' . $aes_ct;
		$packet = strtr(base64_encode($packet), '+/=', '-_,');	 	//	~urlencode
		
		return $packet;
	}

	//----------------------------------------------------------------------------------------------
	//.	decrypt a message sent from this peer, using own private key
	//----------------------------------------------------------------------------------------------
	//arg: cyphertext - encrypted message from this peer [string]

	function decrypt($cyphertext) {
		global $kapenta;

		$privatekey = $kapenta->registry->get('p2p.server.prvkey');
		if ('' == $privatekey) { return 'ERROR: no private key in registry'; }

		$rsa = new Crypt_RSA();
		$aes = new Crypt_AES();

		$cyphertext = strtr($cyphertext, '-_,', '+/=');	//	restore base64 encode padding
		$packet = base64_decode($cyphertext);			//	remove base64 encoding

		$parts = explode('|', $packet, 2);				//	get aes key cyphertext length
		$aeslen = (int)$parts[0];
		$rxMsg = $parts[1];

		$rsa_ct = substr($rxMsg, 0, $aeslen);
		$aes_ct = substr($rxMsg, $aeslen + 1);

		$rsa->loadKey($privatekey);
		$aeskey = $rsa->decrypt($rsa_ct);

		$aes->setKey($aeskey);
		$plaintext = $aes->decrypt($aes_ct);

		return $plaintext;
	}

	//----------------------------------------------------------------------------------------------
	//.	sign a message for this peer
	//----------------------------------------------------------------------------------------------
	//arg: plaintext - raw message to be signed [string]
	//returns: base64_encoded RSA signature (using our private key) [string]

	function sign($plaintext) {
		global $kapenta;

		$privatekey = $kapenta->registry->get('p2p.server.prvkey');
		if ('' == $privatekey) { return 'ERROR: no private key in registry'; }

		$rsa = new Crypt_RSA();
		$rsa->loadKey($privatekey);
		$signature = $rsa->sign(trim($plaintext));
		$signature = base64_encode($signature);
		$signature = strtr($signature, '+/=', '-_,'); 	//	~urlencode
		return $signature;
	}

	//----------------------------------------------------------------------------------------------
	//.	verify a signature against this peer's public key
	//----------------------------------------------------------------------------------------------
	//arg: plaintext - message from peer [string]
	//arg: signature - generated with remote peer's private key [string]
	//returns: true of signature matches message for this peer's public key, false if not [bool]

	function checkSignature($plaintext, $signature) {
		if (true == $this->secure) {
			$rsa = new Crypt_RSA();
			$rsa->loadKey($this->pubkey);

			$signature = strtr($signature, '-_,', '+/='); 		//	restore base64 encode padding
			$signature = base64_decode($signature);

			$ok = $rsa->verify(trim($plaintext), $signature);

			return $ok;
		} else {
			$hash = sha1($plaintext);
			if ($hash == $signature) { return true; }
			return false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	package a message for this peer
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to package for this peer [string]

	function pack($msg, $fileName) {
		global $kapenta;

		if (false == $this->secure) {
			//-------------------------------------------------------------------------------------
			//	encryption not supported by one or both ends
			//-------------------------------------------------------------------------------------
			$signature = sha1($msg);
			$msg = base64_encode($msg);
			$msg = ''
			 . "protocol: fallback-plaintext\n"
			 . "file: " . $fileName . "\n"
			 . "signature: $signature\n"
			 . "message: " . $msg . "\n";

		} else {
			//-------------------------------------------------------------------------------------
			//	both peers support RSA encryption
			//-------------------------------------------------------------------------------------
			$signature = $this->sign($msg);
			$msg = $this->encrypt($msg);
			$msg = ''
			 . "file: " . $fileName . "\n"
			 . "signature: $signature\n"
			 . "message: " . $msg . "\n";
		}


		return $msg;
	}

	//----------------------------------------------------------------------------------------------
	//.	unpack a message from this peer
	//----------------------------------------------------------------------------------------------

	function unpack($data) {

		$msg = array(
			'peer' => $this->UID,
			'file' => '',
			'priority' => '7',
			'signature' => '',
			'verified' => 'no',
			'message' => '',
			'raw' => ''
		);

		$parts = explode("\n", $data);
		foreach($parts as $part) {
			if ('signature:' == substr($part, 0, 10)) { $msg['signature'] = trim(substr($part, 11)); }
			if ('file:' == substr($part, 0, 5)) { $msg['file'] = trim(substr($part, 6)); }
			if ('message:' == substr($part, 0, 8)) { $msg['raw'] = trim(substr($part, 9)); }
		}

		if ('' != $msg['raw']) {
			if (true == $this->secure) { $msg['message'] = $this->decrypt($msg['raw']); }
			else { $msg['message'] = base64_decode($msg['raw']); }
			if ('.bz2' == substr($msg['file'], -4)) { $msg['message'] = p2p_bzdecompress($msg['message']); }
		}

		if (
			('' != $msg['raw']) &&
			(true == $this->checkSignature($msg['message'], $msg['signature']))
		) {
			$msg['verified'] = 'yes';
		}

		//	TEMPORARY, until kinks are works out of RSA signing
		$msg['verified'] = 'yes';

		$parts = explode('.', basename($msg['file']));
		$msg['priority'] = $parts[0];

		return $msg;

	}

}

?>
