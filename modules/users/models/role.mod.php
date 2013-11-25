<?

	require_once(dirname(__FILE__) . '/permissions.set.php');

//--------------------------------------------------------------------------------------------------
//*	object to represent site roles
//--------------------------------------------------------------------------------------------------

class Users_Role {

	//----------------------------------------------------------------------------------------------
	//member variables
	//----------------------------------------------------------------------------------------------

	var $data;				//_	currently loaded database record [array]
	var $dbSchema;			//_	database table definition [array]
	var $loaded = false;	//_	set to true when an object has been loaded [bool]

	var $UID;				//_ UID [string]
	var $name;				//_ title [string]
	var $description;		//_ wyswyg [string]
	var $permissions;		//_ object:Users_PermissionSet [object]
	var $createdOn;			//_ datetime [string]
	var $createdBy;			//_ ref:Users_User [string]
	var $editedOn;			//_ datetime [string]
	var $editedBy;			//_ ref:Users_User [string]
	var $alias;				//_ alias [string]

	//----------------------------------------------------------------------------------------------
	//. constructor
	//----------------------------------------------------------------------------------------------
	//opt: raUID - UID or alias of a Role object [string]
	//opt: byName - set to true if providing role name rather than UID [bool]

	function Users_Role($raUID = '', $byName = false) {
		global $db;

		$this->permissions = new Users_Permissions();

		$this->dbSchema = $this->getDbSchema();				// initialise table schema
		if ('' != $raUID) { 								// try load an object from the database
			if (false == $byName) { $this->load($raUID); }
			else { $this->loadByName($raUID); }
		}			
		if (false == $this->loaded) {						// check if we did
			$this->data = $db->makeBlank($this->dbSchema);	// make new object
			$this->loadArray($this->data);					// initialize
			$this->name = 'New Role ' . $this->UID;			// set default name
			$this->loaded = false;
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of a Role object [string]
	//returns: true on success, false on failure [bool]

	function load($raUID) {
		global $db;
		$objary = $db->loadAlias($raUID, $this->dbSchema);
		if ($objary != false) { $this->loadArray($objary); return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. load an object from the database given its UID or an alias
	//----------------------------------------------------------------------------------------------
	//arg: name - name of a user role [string]
	//returns: true on success, false on failure [bool]

	function loadByName($name) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	try load from memcached
		//------------------------------------------------------------------------------------------
		$cacheKey = 'role::name::' . $name;
		if (true == $kapenta->mc->has($cacheKey)) {
			$UID = $kapenta->mc->get($cacheKey);
			return $this->load($UID);
		}

		//------------------------------------------------------------------------------------------
		//	try load from database
		//------------------------------------------------------------------------------------------
		$conditions = array("name='" . $kapenta->db->addMarkup($name) . "'");
		$range = $kapenta->db->loadRange('users_role', '*', $conditions);
		if (false == $range) { return false; }						// query failed
		if (0 == count($range)) { return false; }					// no role with this name

		foreach($range as $row) {
			//--------------------------------------------------------------------------------------
			//	memcache it for next time
			//--------------------------------------------------------------------------------------
			if (true == $kapenta->mcEnabled) { $kapenta->cacheSet($cacheKey, $row['UID']); }
			return $this->loadArray($row);
		}
	}

	//----------------------------------------------------------------------------------------------
	//. load Role object serialized as an associative array
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of members and values [array]
	//returns: true on success, false on failure [bool]

	function loadArray($ary) {
		global $kapenta;

		if (false == $kapenta->db->validate($ary, $this->dbSchema)) { return false; }

		$this->UID = $ary['UID'];
		$this->name = $ary['name'];
		$this->description = $ary['description'];
		$this->permissions->expand($ary['permissions']);
		$this->createdOn = $ary['createdOn'];
		$this->createdBy = $ary['createdBy'];
		$this->editedOn = $ary['editedOn'];
		$this->editedBy = $ary['editedBy'];
		$this->alias = $ary['alias'];
		$this->loaded = true;

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//. save the current object to database
	//----------------------------------------------------------------------------------------------
	//returns: null string on success, html report of errors on failure [string]
	//: $db->save(...) will raise an object_updated event if successful

	function save() {
		global $db;
		global $aliases;
		global $kapenta;

		$report = $this->verify();
		if ('' != $report) { return $report; }

		$this->alias = $aliases->create('users', 'users_role', $this->UID, $this->name);

		$ary = $this->toArray();
		//echo "<textarea rows='10' cols='80'>{$ary['permissions']}</textarea>";
		$check = $db->save($ary, $this->dbSchema);
		if (false == $check) { return "Database error.<br/>\n"; }

		//	invalidate memcached entry
		$cacheKey = 'role::name::' . $this->name;
		$kapenta->cacheDelete($cacheKey);

		return '';
	}

	//----------------------------------------------------------------------------------------------
	//. check that object is correct before allowing it to be stored in database
	//----------------------------------------------------------------------------------------------
	//returns: null string if object passes, warning message if not [string]

	function verify() {
		$report = '';
		if ('' == $this->UID) { $report .= "No UID.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//. database table definition and content versioning
	//----------------------------------------------------------------------------------------------
	//returns: information for constructing SQL queries [array]

	function getDbSchema() {
		$dbSchema = array();
		$dbSchema['module'] = 'users';
		$dbSchema['model'] = 'users_role';

		//table columns
		$dbSchema['fields'] = array(
			'UID' => 'VARCHAR(33)',
			'name' => 'VARCHAR(255)',
			'description' => 'MEDIUMTEXT',
			'permissions' => 'MEDIUMTEXT',
			'createdOn' => 'DATETIME',
			'createdBy' => 'VARCHAR(33)',
			'editedOn' => 'DATETIME',
			'editedBy' => 'VARCHAR(33)',
			'alias' => 'VARCHAR(255)' );

		//these fields will be indexed
		$dbSchema['indices'] = array(
			'UID' => '10',
			'createdOn' => '',
			'createdBy' => '10',
			'editedOn' => '',
			'editedBy' => '10',
			'alias' => '10' );

		//revision history will be kept for these fields
		$dbSchema['nodiff'] = array();

		return $dbSchema;
		
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to an array
	//----------------------------------------------------------------------------------------------
	//returns: associative array of all members which define this instance [array]

	function toArray() {
		$serialize = array(
			'UID' => $this->UID,
			'name' => $this->name,
			'description' => $this->description,
			'permissions' => $this->permissions->collapse(),
			'createdOn' => $this->createdOn,
			'createdBy' => $this->createdBy,
			'editedOn' => $this->editedOn,
			'editedBy' => $this->editedBy,
			'alias' => $this->alias
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize this object to xml
	//----------------------------------------------------------------------------------------------
	//arg: xmlDec - include xml declaration? [bool]
	//arg: indent - string with which to indent lines [bool]
	//returns: xml serialization of this object [string]

	function toXml($xmlDec = false, $indent = '') {
		//NOTE: any members which are not XML clean should be marked up before sending

		$xml = $indent . "<kobject type='users_role'>\n"
			. $indent . "    <UID>" . $this->UID . "</UID>\n"
			. $indent . "    <name>" . $this->name . "</name>\n"
			. $indent . "    <description><![CDATA[" . $this->description . "]]></description>\n"
			. $indent . "    <permissions>" . $this->collapsePermissions($this->permissions) . "</permissions>\n"
			. $indent . "    <createdOn>" . $this->createdOn . "</createdOn>\n"
			. $indent . "    <createdBy>" . $this->createdBy . "</createdBy>\n"
			. $indent . "    <editedOn>" . $this->editedOn . "</editedOn>\n"
			. $indent . "    <editedBy>" . $this->editedBy . "</editedBy>\n"
			. $indent . "    <alias>" . $this->alias . "</alias>\n"
			. $indent . "</kobject>\n";

		if (true == $xmlDec) { $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n" . $xml;}
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//. make an extended array of data views may need
	//----------------------------------------------------------------------------------------------
	//returns: associative array of members, metadata and partial views [array]

	function extArray() {
		global $user, $utils;
		$ext = $this->toArray();		//% extended array of properties [array:string]

		$ext['viewUrl'] = '';	$ext['viewLink'] = '';
		$ext['editUrl'] = '';	$ext['editLink'] = '';
		$ext['delUrl'] = '';	$ext['delLink'] = '';
		$ext['newUrl'] = '';	$ext['newLink'] = '';
		$ext['goLink'] = $ext['name'];

		//------------------------------------------------------------------------------------------
		//	links
		//------------------------------------------------------------------------------------------
		if (true == $user->authHas('users', 'users_role', 'show', $this->UID)) {
			$ext['viewUrl'] = '%%serverPath%%Users/showrole/' . $ext['alias'];
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
			$ext['viewLink'] = "<a href='" . $ext['viewUrl'] . "'>[ more &gt;gt; ]</a>";
		}

		if (true == $user->authHas('users', 'users_role', 'edit', 'edit', $this->UID)) {
			$ext['editUrl'] = '%%serverPath%%Users/editrole/' . $ext['alias'];
			$ext['editLink'] = "<a href='" . $ext['editUrl'] . "'>[ edit ]</a>";
			$ext['goLink'] = "<a href='" . $ext['editUrl'] . "'>" . $ext['name'] . "</a>";
		}

		if (true == $user->authHas('users', 'users_role', 'edit', 'delete', $this->UID)) {
			$ext['delUrl'] = '%%serverPath%%Users/delrole/' . $ext['alias'];
			$ext['delLink'] = "<a href='" . $ext['delUrl'] . "'>[ delete ]</a>";
		}

		//------------------------------------------------------------------------------------------
		//	javascript
		//------------------------------------------------------------------------------------------
		$ext['UIDJsClean'] = $utils->makeAlphaNumeric($ext['UID']);
		$ext['descriptionJsVar64'] = 'description' . $utils->makeAlphaNumeric($ext['UID']) . 'Js64';
		$ext['descriptionJs64'] = $utils->base64EncodeJs(
			$ext['descriptionJsVar64'], 
			$ext['description']
		);


		return $ext;
	}

	//----------------------------------------------------------------------------------------------
	//. delete current object from the database
	//----------------------------------------------------------------------------------------------
	//: $db->delete(...) will raise an object_deleted event on success [bool]
	//returns: true on success, false on failure [bool]

	function delete() {
		global $db;
		if (false == $this->loaded) { return false; }		// nothing to do
		if (false == $db->delete($this->UID, $this->dbSchema)) { return false; }

		//	invalidate memcached entry
		$cacheKey = 'role::name::' . $this->name;
		$kapenta->cacheDelete($cacheKey);

		return true;
	}

	//==============================================================================================
	//	LEGACY METHODS	//TODO: remove
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	read [permissions] into array for authentication
	//----------------------------------------------------------------------------------------------
	//arg: serialized - permissions set as raw string [string]
	//returns: permissions set as an array [array]

	function expandPermissions($data) {
		global $session;
		$session->msgAdmin('DEPRECATED: Users_Role');
		return $this->permissions->expand($data);
	}

	//----------------------------------------------------------------------------------------------
	//.	collapse permissions array back to an XML string
	//----------------------------------------------------------------------------------------------
	//returns: permissions serialized as a string [string]

	function collapsePermissions() {
		return $this->permissions->collapse();
	}

	//----------------------------------------------------------------------------------------------
	//.	add a permission
	//----------------------------------------------------------------------------------------------
	//arg: type - type of permission (p|c) [string]
	//arg: module - name of a module [string]
	//arg: model - type of object to which permission applies [string]
	//arg: permission - name of an action users may take [string]
	//opt: condition - a relationship, if conditional [string]
	//returns: true if permission was added, false if not [bool]

	function addPermission($type, $module, $model, $permission, $condition = '') {
		return $this->permissions->add($type, $module, $model, $permission, $condition = '');
	}

	//----------------------------------------------------------------------------------------------
	//.	add a permission
	//----------------------------------------------------------------------------------------------
	//arg: type - type of permission (p|c) [string]
	//arg: module - name of a module [string]
	//arg: model - type of object to which permission applies [string]
	//arg: permission - name of an action users may take [string]
	//opt: condition - a relationship, if conditional [string]
	//returns: true if permission was added, false if not [bool]

	function hasPermission($type, $module, $model, $permission, $condition = '') {
		return $this->permissions->has($type, $module, $model, $permission, $condition);
	}

	//----------------------------------------------------------------------------------------------
	//.	temp, for debugging, make this a block
	//----------------------------------------------------------------------------------------------

	function toHtml() {
		return $this->permissions->toHtml();
	}

}

?>
