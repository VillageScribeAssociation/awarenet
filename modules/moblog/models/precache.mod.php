<?

//--------------------------------------------------------------------------------------------------
//*	object for moblog precache
//--------------------------------------------------------------------------------------------------
//+	to avoid making 9001 queries to create a feed combining blog posts by a user's network,
//+	the feed is generated in advance as items are created and removed.
//
//+	the precache is stored as a php array of postUID => timestamp, sorted by timestamp
//
//+	at present precaching is only done for users networks, but could concievably be done for groups,
//+	projects, etc

class Moblog_Precache {

	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record [array]
	var $dbSchema;		// database structure [array]

	var $preCache;		// 

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//arg: refTable - name of a database table [string]
	//arg: refUID - UID [string]

	function MoblogPreCache($refTable, $refUID) {
		global $kapenta;

		if (false == $kapenta->db->objectExists($refTable, $refUID)) { return false; }	
		$this->dbSchema = $this->getDbSchema();
		$UID = $this->preCacheExists($refTable, $refUID);

		if (false == $UID) {
			//--------------------------------------------------------------------------------------
			//	precache doesn't exist, create it
			//--------------------------------------------------------------------------------------
			global $kapenta;
			$this->preCache = array();
			$this->data = $kapenta->db->makeBlank($this->dbSchema);
			$this->refTable = $refTable;
			$this->refUID = $refUID;
			$this->createdOn = $kapenta->db->datetime();
			$this->createdBy = $kapenta->user->UID;
			$this->editedOn = $kapenta->db->datetime();
			$this->editedBy = $kapenta->user->UID;
		

		} else { $this->load($UID); }
		return $UID;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a prcache object [string]

	function load($UID) {
		global $kapenta;
		$ary = $kapenta->db->load($UID, $this->dbSchema);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object provided as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of fields and values [array]

	function loadArray($ary) {
		$this->data = $ary;
		$this->expandPreCache();
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta;

		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->collapsePreCache();
		$kapenta->db->save($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if a precache exists for a given user, returns UID on success
	//----------------------------------------------------------------------------------------------
	//arg: refTable - name of a database table [string]
	//arg: refUID - UID [string]	
	//returns: precache object UID or false if none found [string][bool]

	function preCacheExists($refTable, $refUID) {
		global $kapenta;

		$sql = "select UID from moblogprecache "
			 . "where refTable='" . $kapenta->db->addMarkup($userUID) . "' "
			 . "and refUID='" . $kapenta->db->addMarkup($refUID) . "'";

		//TODO: use dbLoadRange

		$result = $kapenta->db->query($sql);
		if ($kapenta->db->numRows($result) == 0) { return false; }
		$row = $kapenta->db->fetchAssoc($result);
		return $row['UID'];
	}

	//----------------------------------------------------------------------------------------------
	//.	verify - check that a object is valid before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$verify = '';

		if (strlen($this->UID) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//.	sql information
	//----------------------------------------------------------------------------------------------
	//returns: database table layout [array]

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['model'] = 'moblogprecache';
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'refTable' => 'VARCHAR(30)',
			'refUID' => 'VARCHAR(30)',
			'precache' => 'VARCHAR(30)',
			'createdOn' => 'datetime',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'datetime',
			'editedBy' => 'VARCHAR(30)');

		$dbSchema['indices'] = array('UID' => '10', 'refTable' => '5', 'refUID' => '5');
		$dbSchema['nodiff'] = array('UID', 'precache');
		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object as an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all variables which define this instance [array]

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
	//returns: html report lines [string]
	//, deprecated, this should be handled by ../inc/install.inc.php

	function install() {
		global $kapenta;

		$report = "<h3>Installing Moblog Precache</h3>\n";

		if ($kapenta->db->tableExists('moblogprecache') == false) {	
			dbCreateTable($this->dbSchema);	
			$report .= 'created moblog precache...<br/>';

		} else { $report .= 'moblog precache already installed...<br/>'; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	expand precache
	//----------------------------------------------------------------------------------------------

	function expandPreCache() {
		$this->preCache = array();
		$lines = explode("\n", $this->data['precache']);
		foreach ($lines as $line) {
			if (strlen(trim($line)) > 3) {
				$parts = explode("|", $line);
				$this->preCache[$parts[0]] = $parts[1];
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse precache
	//----------------------------------------------------------------------------------------------

	function collapePreCache() {
		$str = '';
		foreach ($this->preCache as $postUID => $timestamp) {
			if ($timestamp != 'delete') { $str .= $postUID . "|" . $timestamp; }
		}
		$this->data['precache'] = $str;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a blog post to the precache
	//----------------------------------------------------------------------------------------------
	//arg: postUID - UID of a blog post [string]
	//arg: timestamp - then the item was added to the cache [int]

	function addItem($postUID, $timestamp) {
		$this->preCache[$postUID] = $timestamp;
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a blog post from the precache
	//----------------------------------------------------------------------------------------------
	//arg: postUID - UID of a blog post [string]
	//returns: true on success, false on failure [bool]

	function removeItem($postUID) {
		if (array_key_exists($postUID, $this->preCache) == true) {
			$this->preCache[$postUID] = 'delete';
			$this->save();
			return true;
		} 
		return false;
	}

}

?>
