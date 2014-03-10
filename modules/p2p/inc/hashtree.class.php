<?

//--------------------------------------------------------------------------------------------------
//*	Hash tree for comparing table states between nodes
//--------------------------------------------------------------------------------------------------

class P2P_HashTree {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;			//_	database table definition [array]
	var $loaded;			//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $table;				//_ varchar(100) [string]
	var $type;				//_ varchar(10) [string]
	var $nodeID;			//_ varchar(50) [string]
	var $children;			//_ text [string]
	var $leafCount;			//_ number of objects (leaves) stored on/below this node [string]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:users_user [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:users_user [string]

	var $parent;			//_	nodeID of parent [string]
	var $children;			//_	pointer to child nodes if banch [array:string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a HashTree object [string]
	//opt: isNodeID - reference is a nodeID + table combination rather than a UID

	function P2P_HashTree($UID = '', $table = '', $isNodeID = false) {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		$this->children = array();
		if ('' != $UID) {
			if (false == $isNodeId) { $this->load($UID); }					// load by UID
			if (true == $isNodeId) { $this->loadNodeID($table, $UID); }	// load by nodeID
		}
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
			$this->children = array();
			$this->type = 'leaf';
			$this->nodeID = '';
			$this->leafCount = 0;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a HashTree object [string]
	//returns: true on success, false on failure [bool]

	function load($UID = '') {
		global $kapenta;
		$objary = $kapenta->db->load('P2P_HashTree', $UID);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a node given its nodeID and table
	//----------------------------------------------------------------------------------------------

	function loadNodeID($table, $nodeID) {
		global $kapenta;
		$this->loadArray($kapenta->db->makeBlank($this->dbSchema);
		$this->table = $table;
		$this->nodeID = $nodeID;
		// TODO
		$this->getParent();
	}

	//----------------------------------------------------------------------------------------------
	//.	load HashTree object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->table = $ary['table'];
		$this->type = $ary['type'];
		$this->nodeID = $ary['nodeID'];
		$this->children = $this->expandChildren($ary['children']);
		$this->leafCount = (int)$ary['leafCount']
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//.	save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $kapenta->db->save(...) will raise an object_updated event if successful

	function save() {
		global $kapenta;
		$report = $this->verify();
		if ('' != $report) { return $report; }
		$check = $kapenta->db->save($this->toArray(), $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//.	check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		if (('leaf' == $this->type) && (0 == count($this->children))) { $report .= 'empty'; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'p2p';
		$dbSchema['model'] = 'p2p_hashtree';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'table' => 'VARCHAR(100)',
			'type' => 'VARCHAR(10)',
			'nodeID' => 'VARCHAR(50)',
			'children' => 'TEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'DATETIME'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'table' => '10',
			'type' => '10',
			'nodeID' => '10',
			'createdOn' => '10',
			'createdBy' => '10',
			'editedOn' => '10',
			'editedBy' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array(
			'table',
			'type',
			'nodeID',
			'children' );

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'table' => $this->table,
			'type' => $this->type,
			'nodeID' => $this->nodeID,
			'children' => $this->collapseChildren($this->children),
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == authHas('view', $ext['UID'])) {
			$ext['viewUrl'] = http://kapenta.co.za/scaffold/showmodeldefinition/190778233531022906;
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == authHas('edit', $ext['UID'])) {
			$ext['editUrl'] = '%~%serverPath%~%p2p/edithashtree/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == authHas('del', $ext['UID'])) {
			$ext['delUrl'] = '%~%serverPath%~%p2p/delhashtree/' . $ext['UID'];
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
	//	expand/collapse children
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//	unserialize child array
	//----------------------------------------------------------------------------------------------

	function expandChildren($serialized) {
		$objects = array();
		$lines = explode("\n", $serialized);
		foreach($lines as $line) {
			if (strlen($line) > 2) {
				$parts = explode($line, "|");

				//----------------------------------------------------------------------------------
				//	leaf nodes contain pointers to database objects
				//----------------------------------------------------------------------------------
				if ('leaf' == $this->type) {
					$objects[$parts[1]] = array(
						'type' => $parts[0],
						'UID' => $parts[1],
						'UIDh' => $parts[2],
						'hash' => $parts[3]
					);
				}

				//----------------------------------------------------------------------------------
				//	branch nodes contain pointers to other nodes
				//----------------------------------------------------------------------------------
				if ('branch' == $this->type) {
					$objects[$parts[1]] = array(
						'type' => $parts[0],
						'nodeID' => $parts[1],
						'count' => (int)$parts[2],
						'hash' => $parts[3]
					}
				}

			}
		}
		return $objects;
	}

	//----------------------------------------------------------------------------------------------
	//	serialize child array
	//----------------------------------------------------------------------------------------------
	
	function collapseChildren($ary) {
		$serialized = '';
		foreach($ary as $item) {
			$serialized .= implode("|", $item) . "\n";
		}
		return $serialized;
	}

	//==============================================================================================
	//	hash tree functions
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	calculate hash of this node
	//----------------------------------------------------------------------------------------------
	//returns: has of this node [string]

	function getHash() {
		$this->hash = sha1($this->nodeID . $this->collapseChildren);
		return $this->hash;
	}

	//----------------------------------------------------------------------------------------------
	//.	get number of objects stored below this one
	//----------------------------------------------------------------------------------------------

	function getLeafCount() {
		if ('leaf' == $this->type) { 
			$this->leafCount = count($this->children); 
			return $this->leafCount;
		}
		$total = 0;
		foreach($this->children as $nodeID => $item) { $total += $item['count']; }
		$this->leafCount = $total;
		return $this->leafCount;
	}

	//----------------------------------------------------------------------------------------------
	//.	set parent of this node
	//----------------------------------------------------------------------------------------------
	//returns: nodeID of this node's parent [string]

	function setParent() {
		$this->nodeID = trim($this->nodeID);
		if ('' == $this->nodeID) { return ''; }		// parent
		$this->parent = substr($this->nodeID, 0, strlen($this->nodeID) - 1);
		return $this->parent;
	}

	//----------------------------------------------------------------------------------------------
	//.	make empty children array for a new branch node
	//----------------------------------------------------------------------------------------------

	function fillEmptyBranch() {
		$this->chidren = array();
		$kids = explode(',', '0,1,2,3,4,5,6,7,8,9,a,b,c,d,e,f');
		foreach($kids as $postfix) {
			$childID = $this->nodeID . $postfix;
			$this->children[$childID] = array(
				'type' => 'c',
				'nodeID' => $childID,
				'count' => 0,
				'hash' => sha1($childID)
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	store an object hash/UID pair in the tree
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a kapenta object [string]
	//arg: UIDh - sha1 hash of UID [string]
	//arg: hash - hash of object (from db driver) [string]
	//returns: true on success, false on failure [bool]

	function set($table, $UID, $UIDh, $hash) {

		//------------------------------------------------------------------------------------------		
		//	if branch node we need to pass this to appropriate child and update self
		//------------------------------------------------------------------------------------------
		if ('branch' == $this->type) {
			$childID = substr($UIDh, 0, strlen($this->nodeID) + 1);
			$child = new P2P_HashTree($childID, $table, true);
			$retVal = $child->set($table, $UID, $UIDh, $hash);

			$this->children[$childID]['count'] = count($child->children);
			$this->children[$childID]['hash'] = $child->getHash();
			//TODO: recalculate my hash and update $this->children and save();

			return $retVal;
		}

		//------------------------------------------------------------------------------------------		
		//	if leaf node we need to pass this to appropriate child
		//------------------------------------------------------------------------------------------
		if ('leaf' == $this->type) {

			//--------------------------------------------------------------------------------------
			//	if full we need to change into a branch node and pass to appropriate child
			//--------------------------------------------------------------------------------------
			if (16 == count($this->children)) {

			}

			//--------------------------------------------------------------------------------------
			//	add to child array and update hash
			//--------------------------------------------------------------------------------------
			$this->children[$UID] = array(
				'type' => 'o',
				'UID' => $UID,
				'UIDh' => $UIDh,
				'hash' => $hash
			);

			$this->getHash();
			$this->save();
			return true;
		}

		return false;		// unknown node type or other error
	}

	//----------------------------------------------------------------------------------------------
	//.	get the hash of an object stored in the tree
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a kapenta object [string]
	//arg: UIDh - sha1 hash of the UID, determines position in tree [string]
	//returns: hash of object or empty string if not found [string]

	function get($UID, $UIDh) {
		//------------------------------------------------------------------------------------------
		//	if this is a branch node we need to pass this request to the next closest child
		//------------------------------------------------------------------------------------------
		if ('branch' == $this->type) {
			$childID = substr($UIDh, 0, strlen($this->nodeID) + 1);
			$child = new P2P_HashTree($childID, $this->table, true);
			$retVal = $child->get($UID, $UIDh);
			return $retVal;	
		}

		//------------------------------------------------------------------------------------------
		//	if this is a leaf node try find the UID
		//------------------------------------------------------------------------------------------
		if ('leaf' == $this->type) {
			if (true == array_key_exists($UID, $this->children)) {
				return $this->children[$UID]['hash'];		// return hash
			} else {
				return '';		// not found
			}
		}

		return false;		// unkown node type
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a UID / hash pair from the tree
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a kapenta object [string]
	//arg: UIDh - sha1 hash of UID - determines position in tree [string]
	//returns: true on success, false on failure [bool]

	function remove($UID, $UIDh) {
		//------------------------------------------------------------------------------------------
		//	if branch we need to pass this down to the next child node
		//------------------------------------------------------------------------------------------
		if ('branch' == $this->type) {
			$childID = substr($UIDh, 0, strlen($this->nodeID) + 1);
			$child = new P2P_HashTree($childID, $this->table, true);
			$retVal = $child->remove($UID, $UIDh);

			$this->children[$childID]['count'] = count($child->children);
			$this->children[$childID]['hash'] = $child->getHash();

			$this->getLeafCount();
			$this->getHash();

			return $retVal;
		}

		if ('leaf' == $this->type) {
			if (false == array_key_exists($UID, $this->children)) { return false; }

			$newChildren = array();
			foreach($this->children as $cUID => $item) {
				if ($UID != $cUID) { $newChildren[$cUID] = $item; }
			}
			$this->children = $newChildren;
			$this->getLeafCount();
			$this->getHash();

		}
	}
	
}

?>
