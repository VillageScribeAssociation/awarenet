<?

//--------------------------------------------------------------------------------------------------
//	object for moblog precache
//--------------------------------------------------------------------------------------------------
//	to avoid making 9001 queries to create a feed combining blog posts by a user's network,
//	the feed is generated in advance as items are created and removed.
//
//	the precache is stored as a php array of postUID => timestamp, sorted by timestamp
//
//	at present precaching is only done for users networks, but could concievably be done for groups,
//	projects, etc

class MoblogPreCache {
	//----------------------------------------------------------------------------------------------
	//	member variables (as retieved from database)
	//----------------------------------------------------------------------------------------------

	var $data;			// currently loaded record
	var $dbSchema;		// database structure

	var $preCache;

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function MoblogPreCache($refTable, $refUID) {
		if (dbRecordExists($refTable, $refUID) == false) { return false; }	
		$UID = $this->preCacheExists($refTable, $refUID);

		if (false == $UID) {
			//--------------------------------------------------------------------------------------
			//	precache doesn't exist, create it
			//--------------------------------------------------------------------------------------
			global $user;
			$this->preCache = array();
			$this->dbSchema = $this->initDbSchema();
			$this->data = dbBlank($this->dbSchema);
			$this->data['refTable'] = $refTable;
			$this->data['refUID'] = $refUID;
			$this->data['createdOn'] = mysql_datetime();
			$this->data['createdBy'] = $user->data['UID'];
			$this->data['editedOn'] = mysql_datetime();
			$this->data['editedBy'] = $user->data['UID'];
		

		} else { $this->load($UID); }
		return $UID;
	}

	//----------------------------------------------------------------------------------------------
	//	load a record by UID or recordAlias
	//----------------------------------------------------------------------------------------------

	function load($uid) {
		$ary = dbLoad('moblogprecache', $uid);
		if ($ary != false) { $this->loadArray($ary); return true; } 
		return false;
	}

	function loadArray($ary) {
		$this->data = $ary;
		$this->expandPreCache();
	}

	//----------------------------------------------------------------------------------------------
	//	save a record
	//----------------------------------------------------------------------------------------------

	function save() {
		$verify = $this->verify();
		if ($verify != '') { return $verify; }

		$this->collapsePreCache();
		dbSave($this->data, $this->dbSchema); 
	}

	//----------------------------------------------------------------------------------------------
	//	discover if a precache exists for a given user, returns UID on success
	//----------------------------------------------------------------------------------------------
	
	function preCacheExists($refTable, $refUID) {
		$sql = "select UID from moblogprecache "
			 . "where refTable='" . sqlMarkup($userUID) . "' "
			 . "and refUID='" . sqlMarkup($refUID) . "'";

		$result = dbQuery($sql);
		if (dbNumRows($result) == 0) { return false; }
		$row = dbFetchAssoc($result);
		return $row['UID'];
	}

	//----------------------------------------------------------------------------------------------
	//	verify - check that a record is correct before allowing it to be stored in the database
	//----------------------------------------------------------------------------------------------

	function verify() {
		$verify = '';

		if (strlen($this->data['UID']) < 5) 
			{ $verify .= "UID not present.\n"; }

		return $verify;
	}

	//----------------------------------------------------------------------------------------------
	//	sql information
	//----------------------------------------------------------------------------------------------

	function initDbSchema() {
		$dbSchema = array();
		$dbSchema['table'] = 'moblogprecache';
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
	//	return the data
	//----------------------------------------------------------------------------------------------

	function toArray() { return $this->data; }

	//----------------------------------------------------------------------------------------------
	//	make and extended array of all data a view will need
	//----------------------------------------------------------------------------------------------

	function extArray() {
		$ary = $this->data;	
		return $ary;
	}

	//----------------------------------------------------------------------------------------------
	//	create blog table if it does not exist
	//----------------------------------------------------------------------------------------------

	function install() {
		$report = "<h3>Installing Moblog Precache</h3>\n";

		if (dbTableExists('moblogprecache') == false) {	
			dbCreateTable($this->dbSchema);	
			$report .= 'created moblog precache...<br/>';

		} else { $report .= 'moblog precache already installed...<br/>'; }

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	expand precache
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
	//	collapse precache
	//----------------------------------------------------------------------------------------------

	function collapePreCache() {
		$str = '';
		foreach ($this->preCache as $postUID => $timestamp) {
			if ($timestamp != 'delete') { $str .= $postUID . "|" . $timestamp; }
		}
		$this->data['precache'] = $str;
	}

	//----------------------------------------------------------------------------------------------
	//	add a blog post to the precache
	//----------------------------------------------------------------------------------------------

	function addItem($postUID, $timestamp) {
		$this->preCache[$postUID] = $timestamp;
		$this->save();
	}

	//----------------------------------------------------------------------------------------------
	//	remove a blog post from the precache
	//----------------------------------------------------------------------------------------------

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
