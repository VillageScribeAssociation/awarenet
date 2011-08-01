<?

//--------------------------------------------------------------------------------------------------
//*	records popularity of content
//--------------------------------------------------------------------------------------------------
//+	each view of an item causes it to displace the item above it on the ladder

class Popular_Ladder {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ name of this ladder [string]
	var $entries;			//_ plaintext [array]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]
	var $shared;			//_ varchar(10), always 'no' for this module [string]

	var $maxEntries = 100;	//_	TODO: consider making this a registry entry [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or name of a Ladder object [string]
	//opt: byName - set to true if loading a ladder by name [bool]

	function Popular_Ladder($raUID = '', $byName = false) {
		global $db;

		$this->entries = array();
		$this->dbSchema = $this->getDbSchema();						// initialise table schema

		if ('' != $raUID) { 										// try load an object
			if (false == $byName) { $this->load($raUID); }			// by UID
			if (true == $byName) { $this->loadByName($raUID); }		// or by name
		}	

		if (false == $this->loaded) {								// check if we did
			$this->loadArray($db->makeBlank($this->dbSchema));		// initialize
			$this->shared = 'no';									// these are never shared
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Ladder object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $db;
		$objary = $db->load($UID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a Popular_Ladder object given its name
	//----------------------------------------------------------------------------------------------
	//arg: name - name of a ladder object [string]
	//returns: true on success, false on failure [bool]

	function loadByName($name) {
		global $db;

		$conditions = array("name='" . $db->addMarkup($name) . "'");
		$range = $db->loadRange('popular_ladder', '*', $conditions);
		foreach($range as $item) {
			$this->loadArray($item);
			return true;
		}

		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load Ladder object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->entries = $this->expandEntries($ary['entries']);
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
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
		return '';
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == trim($this->UID)) { $report .= "No UID.<br/>\n"; }
		if ('' == trim($this->name)) { $report .= "Ladder has no name.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'popular';
		$dbSchema['model'] = 'popular_ladder';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'name' => 'VARCHAR(255)',
			'entries' => 'VARCHAR(255)',
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
			'editedBy' => '10',
			'shared' => '3'
		);

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array(
			'UID',
			'name',
			'entries',
			'createdOn',
			'createdBy',
			'editedOn',
			'editedBy',
			'shared'
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
			'entries' => $this->collapseEntries($this->entries),
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => 'no'
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
		if (true == $user->authHas('popular', 'popular_ladder', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%popular/showladder/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('popular', 'popular_ladder', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%popular/editladder/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('popular', 'popular_ladder', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%popular/delladder/' . $ext['UID'];
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
		global $db;
		if (false == $this->loaded) { return false; }					// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }
		return true;
	}

	//==============================================================================================
	//	entry list
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	unserialize item list
	//----------------------------------------------------------------------------------------------
	//arg: serialized - plaintext of items, one per line [string]
	//returns: array of items [array]

	function expandEntries($serialized) {
		$entries = array();						//%	return value [array]
		$lines = explode("\n", $serialized);	//%	lines of text [array]

		foreach($lines as $line) { 
			if ('' != trim($line)) { $entries[] = $line; }
		}

		return $entries;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize item list
	//----------------------------------------------------------------------------------------------

	function collapseEntries($entries) {
		$serialized = implode("\n", $entries);					//%	return value [string]		
		return $serialized;
	}

	//----------------------------------------------------------------------------------------------
	//.	add an item to the ladder, or bump an item up
	//----------------------------------------------------------------------------------------------
	//arg: item	- item to bump up the list [string]

	function bump($item) {
		$rank = $this->getRank($item);

		if (-1 == $rank) {
			//--------------------------------------------------------------------------------------
			//	not on ladder, add to end or displace last item
			//--------------------------------------------------------------------------------------
			if ($this->maxEntries <= count($this->entries)) {
				$this->entries[$this->maxEntries] = $item;				// displace last item
			} else {
				if (0 == count($this->entries)) { 
					$this->entries[0] = $item;							// first item to be added
				} else {
					$this->entries[count($this->entries) + 1] = $item;	// last item to be added
				}
			}

			$this->save();

		} else {
			//--------------------------------------------------------------------------------------
			//	on ladder bump up
			//--------------------------------------------------------------------------------------
			if (0 == $rank) { 
				// nothing to do, no need to save
			} else {
				$displaced = $this->entries[$rank - 1];
				$this->entries[$rank - 1] = $item;
				$this->entries[$rank] = $displaced;
				$this->save();
			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	get the idnex of an item in the ladder, or -1 if not found
	//----------------------------------------------------------------------------------------------
	//arg: item - item to search for [string]
	//returns: array index of item (0 - n) or -1 if not found [int]

	function getRank($find) {
		foreach($this->entries as $rank => $item) {
			if ($find == $item) { return $rank; }
		}
		return -1;
	}

}

?>
