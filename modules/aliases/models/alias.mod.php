<?

//--------------------------------------------------------------------------------------------------
//*	this object represents object aliases
//--------------------------------------------------------------------------------------------------

class Aliases_Alias {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database object [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	records whether object was loaded from database [bool]

	var $UID;				//_	UID
	var $refModule;			//_	module
	var $refModel;			//_	model
	var $refUID;			//_	ref:*-*
	var $aliaslc;			//_	varchar(255)
	var $alias;				//_	varchar(255)
	var $createdOn;			//_	datetime
	var $createdBy;			//_	ref:users-user
	var $editedOn;			//_	datetime
	var $editedBy;			//_	ref:users-user

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: UID - UID of a Alias object [string]

	function Aliases_Alias($UID = '') {
		global $db;
		$this->dbSchema = $this->getDbSchema();					// initialise table schema
		if ('' != $UID) { $this->load($UID); }					// try load an object, if given
		if (false == $this->loaded) { 
			$this->loadArray($db->makeBlank($this->dbSchema));
			$this->loaded = false;
		}
	}


	//----------------------------------------------------------------------------------------------
	//. load an object from the db given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Alias object [string]
	//returns: true on success, false on failure [bool]

	function load($UID) {
		global $db;
		$ary = $db->load($UID, $this->dbSchema);
		if ($ary != false) { $this->loadArray($ary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load Alias object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$this->data = $ary;
		$this->UID = $ary['UID'];
		$this->refModule = $ary['refModule'];
		$this->refModel = $ary['refModel'];
		$this->refUID = $ary['refUID'];
		$this->aliaslc = $ary['aliaslc'];
		$this->alias = $ary['alias'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->loaded = true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]

	function save() {
		global $kapenta, $db;
		$report = $this->verify();
		if ('' != $report) { return $report; }

		//	clear any previous version from memcached
		if (true == $kapenta->mcEnabled) {
			$kapenta->cacheDelete('alias::' . $this->refModel . '::' . strtolower($this->alias));
			$kapenta->cacheDelete('aliasalt::' . $this->refModel . '::' . strtolower($this->alias));
		}

		$check = $db->save($this->toArray(), $this->dbSchema);

		if (true == $check) { return ''; }
		else { return "Database error: " . $db->lasterr . "\n"; }
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';

		$this->alias = trim($this->alias);
		$this->aliaslc = strtolower($this->alias);
		if ('' == $this->UID) { $report .= "No UID.\n"; }
		if ('' == $this->alias) { $report .= "No alias.\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'aliases';
		$dbSchema['model'] = 'aliases_alias';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'refModule' => 'VARCHAR(50)',
			'refModel' => 'VARCHAR(50)',
			'refUID' => 'VARCHAR(30)',
			'aliaslc' => 'VARCHAR(255)',
			'alias' => 'VARCHAR(255)',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'refModule' => '10',
			'refModel' => '10',
			'refUID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$ser = array(
			'UID' => $this->UID,
			'refModule' => $this->refModule,
			'refModel' => $this->refModel,
			'refUID' => $this->refUID,
			'aliaslc' => $this->aliaslc,
			'alias' => $this->alias,
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy
		);
		return $ser;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended arry of data views may need
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
		if (true == $user->authHas('aliases', 'aliases_alias', 'show', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%alias/showalias/' . $ext['UID'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more >gt; ]</a>";
		}

		if (true == $user->authHas('aliases', 'aliases_alias', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%alias/editalias/' . $ext['UID'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $user->authHas('aliases', 'aliases_alias', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%alias/delalias/' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database and memory cache
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function delete() {
		global $kapenta;
		global $db;
		if (false == $this->loaded) { return false; }
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }

		if (true == $kapenta->mcEnabled) {
			$kapenta->cacheDelete('alias::' . $this->refModel . '::' . strtolower($this->alias));
			$kapenta->cacheDelete('aliasalt::' . $this->refModel . '::' . strtolower($this->alias));
		}

		return true;
	}

}

?>
