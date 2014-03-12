<?

//--------------------------------------------------------------------------------------------------
//*	Stored user registry templates (used for applying color themes)
//--------------------------------------------------------------------------------------------------


class Users_Preset {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $dbSchema;					//_	database table definition [array]
	var $loaded;					//_	set to true when an object has been loaded [bool]

	var $UID;						//_ UID [string]
	var $title;						//_ title [string]
	var $description;				//_ description of this registry template [string]
	var $cat;						//_ type of registry template (theme only for now) [string]
	var $settings;					//_ serialized color set [string]
	var $createdOn;					//_ datetime [string]
	var $createdBy;					//_ ref:users_user [string]
	var $editedOn;					//_ datetime [string]
	var $editedBy;					//_ ref:users_user [string]
	var $shared;					//_ shared [string]
	var $alias;						//_ alias [string]

	var $registry;					//_	[object:Users_Settings]
	var $registryLoaded = false;	//_	set to true when registry loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Preset object [string]

	function Users_Preset($raUID = '') {
		global $kapenta;
		$this->dbSchema = $this->getDbSchema();		// initialise table schema
		$this->registry = new Users_Settings();

		if ('' != $raUID) { $this->load($raUID); }	// try load an object from the database
		if (false == $this->loaded) {			// check if we did
			$this->loadArray($kapenta->db->makeBlank($this->dbSchema));	// initialize
			$this->loaded = false;
			$this->registryLoaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Preset object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $kapenta;
		$objary = $kapenta->db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load preferences of a specified user
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Users_User object [string]
	//returns: true on success or false on failure [bool]

	function loadUserTheme($raUID) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	load the user
		//------------------------------------------------------------------------------------------
		$fromuser = new Users_User($raUID);
		if (false == $fromuser->loaded) { return false; }

		//------------------------------------------------------------------------------------------
		//	registry values are default and may be overridden by per-user settings
		//------------------------------------------------------------------------------------------

		$colors = explode('|', $kapenta->registry->get('theme.colors'));		//% [array]
		$images = explode('|', $kapenta->registry->get('theme.images'));		//% [array]

		foreach($colors as $color) {
			$key = 'theme.c.' . $color;
			$this->registry->set($key, $kapenta->registry->get($key));
			$check = $fromuser->get('ut.c.' . $color);
			if ('' != $check) { $this->registry->set($key, $check); }
		}

		foreach($images as $image) {
			$key = 'theme.i.' . $image;
			$this->registry->set($key, $kapenta->registry->get($key));
			$check = $fromuser->get('ut.i.' . $image);
			if ('' != $check) { $this->registry->set($key, $check); }
		}

		$this->registryLoaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load Preset object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]

	function loadArray($ary) {
		if (false == is_array($ary)) { return false; }
		$serialize = $ary;
		$this->UID = $ary['UID'];
		$this->title = $ary['title'];
		$this->description = $ary['description'];
		$this->cat = $ary['cat'];
		$this->settings = $ary['settings'];
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->shared = $ary['shared'];
		$this->alias = $ary['alias'];

		$this->registry = new Users_Settings($this->settings);
		$this->registryLoaded = true;

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
		$this->alias = $aliases->create('users', 'users_preset', $this->UID, $this->title);
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
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'users_preset';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(30)',
			'title' => 'VARCHAR(255)',
			'description' => 'MEDIUMTEXT',
			'cat' => 'VARCHAR(10)',
			'settings' => 'MEDIUMTEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(30)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(30)',
			'shared' => 'VARCHAR(3)',
			'alias' => 'VARCHAR(255)'
		);

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'title' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'shared' => '1',
			'alias' => '10'
		);

		//revision history will be kept for these fields
		$dbSchema['diff'] = array('title');

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'title' => $this->title,
			'description' => $this->description,
			'cat' => $this->cat,
			'settings' => $this->registry->toString(),
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'shared' => $this->shared,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//.	serialize this object to a set of registry keys
	//----------------------------------------------------------------------------------------------
	//returns: set of key => value pairs [dict]

	function getRegistryKeys() {
		$dict = array();
		foreach($this->registry->members as $key => $value) {
			$dict[$key] = $this->registry->get($key);
		}
		return $dict;
	}

	//----------------------------------------------------------------------------------------------
	//.	make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $kapenta;
		$ext = $this->toArray();

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $kapenta->user->authHas('users', 'users_preset', 'view', $ext['UID'])) {
			$ext['viewUrl'] = '%%serverPath%%users/showpreset/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;&gt; ]</a>";
		}

		if (true == $kapenta->user->authHas('users', 'users_preset', 'edit', $ext['UID'])) {
			$ext['editUrl'] = '%%serverPath%%users/editpreset/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
		}

		if (true == $kapenta->user->authHas('users', 'users_preset', 'delete', $ext['UID'])) {
			$ext['delUrl'] = '%%serverPath%%users/confirmdeletepreset/UID_' . $ext['UID'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[delete]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	copy registry keys into ext
		//------------------------------------------------------------------------------------------
		$settings = $this->registry->toArray();
		foreach($settings as $key => $value) { $ext[$key] = $value; }

		//------------------------------------------------------------------------------------------
		//	make a 300px wide background
		//------------------------------------------------------------------------------------------

		$ext['bgImage'] = ''
		 . "<div style='"
			 . "width: 300px; height: 200px; background-color:"
			 . $this->registry->get('theme.c.background') . ';'
		 . "'></div>";

		if ('' != $this->registry->get('theme.i.background')) {
			$ext['bgImage'] = ''
			 . "<img src='"
				 . "%%serverPath%%" . $this->registry->get('theme.i.background')
			 . "' width='300px' />";
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

	//----------------------------------------------------------------------------------------------
	//.	apply this preset to a user account
	//----------------------------------------------------------------------------------------------
	//arg: userUID - UID or alias of a Users_User object [string]
	//returns: true on success, false on failure [bool]

	function applyTo($userUID) {
		if (false == $this->registryLoaded) { return false; }
		$model = new Users_User($userUID);
		if (false == $model->loaded) { return false; }

		$settings = $this->registry->toArray();

		$model->set('ut.i.background', '');

		foreach($settings as $key => $value) {
			$key = str_replace('theme.', 'ut.', $key);
			$model->set($key, $value);
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	apply this preset as site default
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	function makeDefault() {
		global $kapenta;
		if (false == $this->registryLoaded) { return false; }
		$settings = $this->registry->toArray();
		$kapenta->registry->set('theme.i.background', '');
		foreach($settings as $key => $value) { $kapenta->registry->set($key, $value); }
		return true;		
	}

}

?>
