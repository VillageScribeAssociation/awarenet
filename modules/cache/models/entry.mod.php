<?

//--------------------------------------------------------------------------------------------------
//*	Maintains pre-generated block cache
//--------------------------------------------------------------------------------------------------

class Cache_Entry {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $tag;				//_ title [string]
	var $role;				//_ role of user [string]
	var $area;				//_ content area on page (nav1, content, menu1, etc) [string]
	var $content;			//_ text [string]
	var $channel;			//_ module defined, used for invalidation [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ shared [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Entry object [string]

	function Cache_Entry($UID = '') {
		global $db;
		global $kapenta;
		global $session;
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	force clear the cache when the serverPath changes (invalidates everything)
		//------------------------------------------------------------------------------------------

		$sp = $kapenta->registry->get('kapenta.serverpath');
		$lp = $kapenta->registry->get('cache.serverpath');

		if ($lp != $sp) {
			$sql = "delete from cache_entry;";
			$db->query($sql);
			$kapenta->registry->set('cache.serverpath', $sp);
			$session->msgAdmin("Server connected to new network - clearing cache.", 'ok');
		}

		//------------------------------------------------------------------------------------------
		//	continue loading as usual
		//------------------------------------------------------------------------------------------

		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		if ('' != $UID) { $this->load($UID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
			$this->shared = 'no';
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Entry object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}


	//----------------------------------------------------------------------------------------------
	//.	load Entry object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->tag = $ary['tag'];
		$this->role = $ary['role'];
		$this->area = $ary['area'];
		$this->content = $ary['content'];
		$this->channel = $ary['channel'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;


		$this->shared = 'no';						//	these are never shared with other peers

		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
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
		$dbSchema['module'] = 'cache';
		$dbSchema['model'] = 'cache_entry';
		$dbSchema['archive'] = 'no';				//	no revisions are kept for these

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'tag' => 'VARCHAR(255)',
			'role' => 'VARCHAR(30)',
			'area' => 'VARCHAR(10)',
			'content' => 'MEDIUMTEXT',
			'channel' => 'VARCHAR(100)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'tag' => '30',
			'channel' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'tag',
			'role',
			'area',
			'content',
			'channel'
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
			'tag' => $this->tag,
			'role' => $this->role,
			'area' => $this->area,
			'content' => $this->content,
			'channel' => $this->channel,
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

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('cache', 'cache_entry', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%cache/showentry/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $user->authHas('cache', 'cache_entry', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%cache/editentry/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('cache', 'cache_entry', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%cache/delentry/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		global $db;

		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }

		// invalidate
		$cacheKey = 'dbcache::' . $this->area . '::' . $this->role . '::' . $this->tag;
		$kapenta->cacheDelete($cacheKey);

		return true;
	}

}

?>
