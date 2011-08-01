<?

//--------------------------------------------------------------------------------------------------
//*	database driver (abstraction object) for mysql
//--------------------------------------------------------------------------------------------------
//+ These are wrapper functions to allow the same function names (and thus code) on different DBMS.
//+ Note that % (sql wildcard) is stripped from UIDs from some functions as a security precaution.
//+ Field values are generally escaped to prevent SQL injection by sqlMarkup
//+
//+ Table schema are passed as nested associative arrays:
//+
//+  'table' -> string, name of table
//+  'fields' -> array of fieldname -> type, where type is MySQL type
//+  'indices' -> array of fieldname -> size (index name is derived from fieldname)
//+	 'nodiff' -> array of field names which are not versioned (eg, hitcount)
//+
//+ NOTE: additional database functionality is provides in mysqladmin, this functionality was moved
//+	to this file because most user actions will not need it, and it keeps the default loaded
//+ codebase down.

class KDBDriver {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $host = '';				//_	database host [string]
	var $user = '';				//_ database user name [string]
	var $pass = '';				//_	database password [string]
	var $name = '';				//_ database name [string]

	var $cache;					//_	cache of objects loaded from database [array]
	var $aliases;				//_	cache of alias => object mappings [array]
	var $cacheSize = 200;		//_	maximum bumber of objects to cache [int]

	var $tables;				//_	set of tables in this database [array]
	var $tablesLoaded = false;	//_	set to true when list of tables has been loaded [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: dbHost - database host, default is 127.0.0.1 [string]
	//opt: dbUser - database user, default is root [string]
	//opt: dbPass - database password, no default [string]
	//opt: dbName - database name, default is 'kapenta' [string]

	function KDBDriver() {
		global $registry;			// perhaps get these from $kapenta

		$this->host = $registry->get('kapenta.db.host');
		$this->user = $registry->get('kapenta.db.user');
		$this->pass = $registry->get('kapenta.db.password');
		$this->name = $registry->get('kapenta.db.name');
		
		$this->cache = array();
		$this->aliases = array();
		$this->tables = array();
	}

	//----------------------------------------------------------------------------------------------
	//.	execute a query, return handle
	//----------------------------------------------------------------------------------------------
	//arg: query - a SQL query [string]
	//returns: handle to query result or false on failure [int][bool]

	function query($query) {
		global $session, $page, $registry;
		$connect = false;							//%	database connection handle [int]
		$selected = false;							//%	database selection [bool]
		$result = false;							//%	recordset handle [int]

		$page->logDebug('query', $query);

		//------------------------------------------------------------------------------------------
		// connect to database server and select database
		//------------------------------------------------------------------------------------------
		if ('yes' == $registry->get('kapenta.db.persistent')) {
			$connect = @mysql_pconnect($this->host, $this->user, $this->pass);
		} else {
			$connect = @mysql_connect($this->host, $this->user, $this->pass);
		}

		if (false === $connect) { 
			$session->msgAdmin('Could not connect to database server.' , 'bad');
			return false; 
		}

		$selected = @mysql_select_db($this->name, $connect);
		if (false == $selected) {
			$msg = "Connected to database server but could not select database: " . $this->name;
			$session->msgAdmin($msg , 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		// connect to database server and select database
		//------------------------------------------------------------------------------------------
		$result = @mysql_query($query, $connect);
		if (false === $result) {
			$msg = "Could not execute database query:<br/>" . $query . "<hr/><br/>" . mysql_error();
			$session->msgAdmin($msg, 'bad');
			return false;
		}

		return $result;		// handle to results
	}

	//----------------------------------------------------------------------------------------------
	//.	check that a serialized object matches the schema it will fill
	//----------------------------------------------------------------------------------------------
	//arg: serialized - associative array of fields and values [array]
	//arg: UID - UID of record to load
	//returns: return associative array of field names and values or false on failure [array] [bool]

	function validate($serialized, $dbSchema) {
		if (false == is_array($serialized)) { return false; }
		foreach($dbSchema['fields'] as $fName => $dbType) 
			{ if (false == array_key_exists($fName, $serialized)) { return false; } }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object given it's type and UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of record to load [string]
	//arg: dbSchema - database table definition [array]
	//returns: return associative array of field names and values or false on failure [array][bool]

	function load($UID, $dbSchema) {
		global $page;
		$model = strtolower($dbSchema['model']);
		if (false == $this->tableExists($model)) { return false; }

		//------------------------------------------------------------------------------------------
		//	check the cache for this object
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == array_key_exists($cacheKey, $this->cache)) {
			return $this->cache[$cacheKey]; 
			$page->logDebug('dbLoad', 'cache hit: ' . $model . '::' . $UID);
		} else {
			$page->logDebug('dbLoad', 'cache miss: ' . $model . '::' . $UID);
		}

		//------------------------------------------------------------------------------------------
		//	object is not in cache, try load from database
		//------------------------------------------------------------------------------------------

		$sql = "SELECT * FROM " . $model . " where UID='" . $this->addMarkup($UID) . "'";
		$recordSet = $this->query($sql);
		if (false == $recordSet) { return false; }
		while ($record = mysql_fetch_assoc($recordSet))	 {
			//--------------------------------------------------------------------------------------
			// object found - strip database markup, store in cache and return
			//--------------------------------------------------------------------------------------
			$retVal = array();	
			foreach($record as $fName => $fVal) { $retVal[$fName] = $this->removeMarkup($fVal); }
			$this->cacheStore($model, $retVal);
			return $retVal;
		}

		$page->logDebug('dbLoad', 'no such object: ' . $model . '::' . $UID);
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record from a table supporting recordAliases, return associative array
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of an object [string]
	//arg: dbSchema - database table definition [array]
	//returns: return associative array of field names and values or false on failure [array] [bool]

	function loadAlias($raUID, $dbSchema) {
		global $page;
		$model = strtolower($dbSchema['model']);
		if (false == $this->tableExists($model)) { return false; }

		//------------------------------------------------------------------------------------------
		//	try load from cache by UID
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $raUID;
		if (true == array_key_exists($cacheKey, $this->cache)) { 
			$page->logDebug('dbLoad', 'cache hit: ' . $model . '::' . $raUID . ' (loadAlias)');
			return $this->cache[$cacheKey]; 
		}

		//------------------------------------------------------------------------------------------
		//	try load from cache by Alias
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . strtolower($raUID);
		if (true == array_key_exists($cacheKey, $this->aliases)) {
			$UID = $this->aliases[$cacheKey];
			$page->logDebug('dbLoad', 'alias hit: ' . $model . '::' . $raUID . ' => ' . $UID . '');
			return $this->load($UID, $dbSchema);
		}		

		//------------------------------------------------------------------------------------------
		//	not found in cache, try load from database
		//------------------------------------------------------------------------------------------
		$page->logDebug('dbLoad', 'cache miss: ' . $model . '::' . $raUID . ' (loadAlias)');

		$sql = "SELECT * FROM " . $model 
			. " WHERE alias='" . $this->addMarkup($raUID) . "'"
			. " OR UID='" . $this->addMarkup($raUID) . "'";

		$recordSet = $this->query($sql);
		while ($record = mysql_fetch_assoc($recordSet))	 {
			//--------------------------------------------------------------------------------------
			// object found - strip database markup, store in cache and return
			//--------------------------------------------------------------------------------------
			$retVal = array();
			foreach($record as $fName => $fVal) { $retVal[$fName] = $this->removeMarkup($fVal); }	 
			$this->cacheStore($model, $retVal);
			return $retVal;
		}

		//------------------------------------------------------------------------------------------
		//	not found in table, try other aliases
		//------------------------------------------------------------------------------------------
		if (true == $this->tableExists('aliases_alias')) {
			$sql = "SELECT * FROM aliases_alias "
				 . "WHERE refModel='" . $this->addMarkup($model) . "' "
				 . "AND aliaslc='" . $this->addMarkup(strtolower($raUID)) . "'";

			$result = $this->query($sql);
			if (false === $result) { return false; }								// query failed
			
			while ($row = $this->fetchAssoc($result)) {								// try all 
				if (false == array_key_exists('refUID', $row)) { return false; }
				$refUID = $this->removeMarkup($row['refUID']);
				if (true == $this->objectExists($model, $refUID)) {
					return $this->load($refUID, $dbSchema);
				}
			}

		}

		//------------------------------------------------------------------------------------------
		//	out of options
		//------------------------------------------------------------------------------------------
		$page->logDebug('dbLoad', 'no such object: ' . $model . '::' . $raUID);
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	store an object in the cache
	//----------------------------------------------------------------------------------------------
	//arg: model - type of object being stored [string]
	//arg: objAry - associative array of object members [string]

	function cacheStore($model, $objAry) {
		$this->cache[$model . '::' . $objAry['UID']] = $objAry;		
		if (true == array_key_exists('alias', $objAry))
			{ $this->aliases[$model . '::' . strtolower($objAry['alias'])] = $objAry['UID']; }
		if (count($this->cache) > $this->cacheSize) { $discard = array_shift($this->cache); }
		if (count($this->aliases) > $this->cacheSize) { $discard = array_shift($this->aliases); }
	}

	//----------------------------------------------------------------------------------------------
	//.	save a record given a dbSchema array and an associative array of fields and values
	//----------------------------------------------------------------------------------------------
	//arg: data - serialized object [array]
	//arg: dbSchema - schema of the database table this record belongs in [array]
	//opt: setdefaults - if true will automatically modify editedBy, editedOn, etc [bool]
	//opt: broadcast - if true, then pass to other peers [bool]
	//opt: revision - if true, then keep any revision data [bool]
	//returns: true on success, false on failure [bool]

	function save($data, $dbSchema, $setdefaults = true, $broadcast = true, $revision = true) {
		global $user, $revisions, $sync, $session, $kapenta;
		if (array_key_exists('UID', $data) == false) { return false; }		// must have a UID
		if (strlen(trim($data['UID'])) < 4) { return false; }				// and it must be good

		$dbSchema['model'] = strtolower($dbSchema['model']);				// temporary

		if (false == $this->tableExists($dbSchema['model'])) { return false; }
		//TODO: check schema and consider auto table installation

		//------------------------------------------------------------------------------------------
		//	set editedBy, editedOn if present in schema
		//------------------------------------------------------------------------------------------
		if (true == $setdefaults) {
			if (true == array_key_exists('editedBy', $dbSchema['fields']))
				{ $data['editedBy'] = $user->UID; }
			if (true == array_key_exists('editedOn', $dbSchema['fields']))
				{ $data['editedOn'] = $this->datetime(); }
		}

		//------------------------------------------------------------------------------------------
		//	try load previous version of this record
		//------------------------------------------------------------------------------------------
		$current = $this->load($data['UID'], $dbSchema);

		//------------------------------------------------------------------------------------------
		//	replace any previously cached version
		//------------------------------------------------------------------------------------------
		$this->cacheStore($dbSchema['model'], $data);

		//------------------------------------------------------------------------------------------
		//	if no previous version exists, save this one // TODO: consider adding revision here
		//------------------------------------------------------------------------------------------
		if (false == $current) {
			$newFields = array();
			foreach ($dbSchema['fields'] as $fName => $fType) {
				$value = '';
				if (true == array_key_exists($fName, $data))
					{ $value = $this->addMarkup($data[$fName]); }		// prevent SQL injection
				if (true == $this->quoteType($fType)) 
					{ $value = "\"" . $value . "\""; }					// quote string values
				$newFields[$fName] = $value;
			}

			$sql = "INSERT INTO " . $dbSchema['model']
				 . " values (" . implode(', ', $newFields) . ");";		// assemble the query

			$result = $this->query($sql);								// run it...

			//--------------------------------------------------------------------------------------
			//	allow other modules to respond to this event	//TODO: consider expanding args
			//--------------------------------------------------------------------------------------
			$args = array(
				'module' => $dbSchema['module'],
				'model' => $dbSchema['model'],
				'UID' => $data['UID'],
				'data' => $data
			);

			$kapenta->raiseEvent('*', 'object_updated', $args);

			//--------------------------------------------------------------------------------------
			//	broadcast to peers
			//--------------------------------------------------------------------------------------
			if (true == $broadcast) { $sync->broadcastDbUpdate('self', $dbSchema['model'], $data); }
			if (false === $result) { return false; }					// could not save
			return true;												// OK, saved
		}

		//------------------------------------------------------------------------------------------
		//	pervious version does exist, find if/where it differs from this one
		//------------------------------------------------------------------------------------------

		$changes = array();
		$dirty = false;
		foreach($current as $fName => $fVal) {
		    if ((true == array_key_exists($fName, $data)) && ($fVal != $data[$fName])) {	
				$changes[$fName] = $this->addMarkup($data[$fName]); 	//	this field has changed
				if (true == is_array($dbSchema['nodiff'])) { 
					if (false == array_key_exists($fName, $dbSchema['nodiff'])) { $dirty = true; }
				}
		    }
		}

		if (0 == count($changes)) { return true; }						// nothing to do

		//------------------------------------------------------------------------------------------
		//	record revisions to any fields for swhich we track changes
		//------------------------------------------------------------------------------------------
		if ((true == $revision) && (true == $dirty) && (true == $setdefaults)) {
			$revisions->storeRevision($changes, $dbSchema);
		}

		//------------------------------------------------------------------------------------------
		//	make changes to stored record
		//------------------------------------------------------------------------------------------
		foreach($changes as $fName => $fVal) {
			if (true == array_key_exists($fName, $dbSchema['fields'])) {
				if (true == $this->quoteType($dbSchema['fields'][$fName]))
					{ $fVal = "\"" . $fVal . "\""; }
			}
			$changes[$fName] = "`" . $fName . "`" . '=' . $fVal;
		}

		$sql = "UPDATE " . $dbSchema['model'] 
			 . " SET " . implode(', ', $changes)
			 . " WHERE UID='" . $this->addMarkup($data['UID']) . "';";

		//$session->msg($sql, 'ok');

		$result = $this->query($sql);

		if (false === $result) {
			$msg = 'could not update record: ' . $dbSchema['model'] . " " . $data['UID'];
			$session->msgAdmin($msg, 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	allow other modules to respond to this event	//TODO: consider expanding args
		//------------------------------------------------------------------------------------------		
		$args = array(
			'module' => $dbSchema['module'],
			'model' => $dbSchema['model'],
			'UID' => $data['UID'],
			'data' => $data
		);

		$kapenta->raiseEvent('*', 'object_updated', $args);

		//------------------------------------------------------------------------------------------
		//	pass on to peers, and we're done
		//------------------------------------------------------------------------------------------		
		if ((true == array_key_exists('shared', $data)) && ('no' == $data['shared'])) {
			$broadcast = false; 
		}
		if (true == $broadcast) { $sync->broadcastDbUpdate('self', $dbSchema['model'], $data); }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	update a record field without causing resync, strings only
	//----------------------------------------------------------------------------------------------
	//arg: model - model name / name of database table [string]
	//arg: UID - UID of a record [string]
	//arg: field - field name [string]
	//arg: value - field value [string]
	//: this is used where an object should change only locally and changes not sent to peer servers

	function updateQuiet($model, $UID, $field, $value) {	
		$model = strtolower($model);									// temporary
		if (false == $this->tableExists($model)) { return false; }

		//TODO: improve this

		$sql = "UPDATE " . $model . " "
			 . "SET " . $this->addMarkup($field) . "='" . $this->addMarkup($value) . "' "
			 . "WHERE UID='" . $this->addMarkup($UID) . "'";

		//TODO: update cache
		//TODO: process triggers

		$this->query($sql);
	}

	//----------------------------------------------------------------------------------------------
	//|	determine is a field should be quoted in SQL queries, given its type
	//----------------------------------------------------------------------------------------------
	//arg: dbType - MySQL field type [int]
	//returns: true if fields of this type should be quoted [array]

	function quoteType($dbType) {
		$dbType = strtolower($dbType);
		switch($dbType) {
			case 'tinyint':		return false;
			case 'smallint':	return false;
			case 'mediumint':	return false;
			case 'int':			return false;
			case 'bigint':		return false;
			case 'float':		return false;
			case 'double':		return false;
			case 'real':		return false;
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//|	get a row from a recordset,
	//----------------------------------------------------------------------------------------------
	//arg: handle - handle to a MySQL query result [int]
	//returns: associative array of field names and values, database markup not removed [array]

	function fetchAssoc($handle) { 
		if (false === $handle) { return false; }
		return mysql_fetch_assoc($handle); 
	}

	//----------------------------------------------------------------------------------------------
	//|	get number of rows in recordset
	//----------------------------------------------------------------------------------------------
	//arg: handle - handle to a MySQL query result [int]
	//returns: number of rows or false on failure [int] [bool]

	function numRows($handle) { 
		if (false === $handle) { return false; }
		return mysql_num_rows($handle); 
	}

	//----------------------------------------------------------------------------------------------
	//.	delete an object (if it exists)
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / name of database table [string]
	//arg: UID - UID of a record [string]
	//returns: true on success, false on failure [bool]

	function delete($UID, $dbSchema) {
		global $kapenta, $aliases, $revisions;
		
		$module = $dbSchema['module'];
		$model = strtolower($dbSchema['model']);			// TODO: remove strtolower when safe

		// this also checks that table exists:
		if (false == $this->objectExists($model, $UID)) { return false; }	
		
		if ('users_user' == $model) { return false; }		//	SPECIAL CASE, security
		if ('schools_school' == $model) { return false; }	//	SPECIAL CASE, security

		//------------------------------------------------------------------------------------------
		//	copy to recycle bin (unless set to 'no' archive)
		//------------------------------------------------------------------------------------------
		if ((false == array_key_exists('archive', $dbSchema)) || ('no' != $dbSchema['archive'])) {
			$fields = $this->load($UID, $dbSchema);
			foreach($fields as $name => $value) { $fields[$name] = $this->addMarkup($value); }
			$revisions->recordDeletion($fields, $dbSchema);
		}

		//------------------------------------------------------------------------------------------
		//	remove from original table
		//------------------------------------------------------------------------------------------
		$sql = "DELETE FROM " . $model . " WHERE UID='" . $this->addMarkup($UID) . "'";
		$this->query($sql);

		//------------------------------------------------------------------------------------------
		//	remove from cache
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == array_key_exists($cacheKey, $this->cache)) {
			$objAry = $this->load($UID, $dbSchema);			
			unset($this->cache[$cacheKey]);
			if (true == array_key_exists('alias', $objAry)) { 
				$aliasKey = $model . '::' . strtolower($objAry['alias']);
				unset($this->aliases[$aliasKey]); 
			}
		}

		//syncRecordDeletion($model, $UID);

		//------------------------------------------------------------------------------------------
		//	delete any aliases this item might have
		//------------------------------------------------------------------------------------------
		$aliases->deleteAll($module, $model, $UID);

		//------------------------------------------------------------------------------------------
		//	send event to any modules which may need to do something about this
		//------------------------------------------------------------------------------------------
		$detail = array('module' => $module, 'model' => $model, 'UID' => $UID);
		$kapenta->raiseEvent($module, 'object_deleted', $detail);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	create an associative array of a recordset (expects UID for index)
	//----------------------------------------------------------------------------------------------
	//arg: sql - sql query [string]
	//returns: array of associative arrays, database markup not removed [array]
	//:	TODO: discover if this is used by anything, if not, remove

	function queryToArray($sql) {
		$result = $this->query($sql);
		$recordSet = array();
		while ($row = mysql_fetch_assoc($result)) {
			$recordSet[$row['UID']] = $row;
		}
		return $recordSet;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if an object of a given type exists
	//----------------------------------------------------------------------------------------------
	//arg: model - model name / name of database table [string]
	//arg: UID - UID of a record [string]
	//returns: true if record exists in the given table, false if not found [bool]

	function objectExists($model, $UID) {
		$model = strtolower($model);								// temporary

		//------------------------------------------------------------------------------------------
		//	if we already have this object in the cache, assume it exists
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == array_key_exists($cacheKey, $this->cache)) { return true; }

		//------------------------------------------------------------------------------------------
		//	not in cache, try database
		//------------------------------------------------------------------------------------------
		if (false == $this->tableExists($model)) { return false; }	// prevent SQL injection
		$sql = "SELECT * FROM $model WHERE UID='" . $this->addMarkup($UID) . "'";		
		$result = $this->query($sql);
		if (false === $result) { return false; } 					// bad table name?
		if (0 == mysql_num_rows($result)) { return false; }			// no such object

		//------------------------------------------------------------------------------------------
		// object exists, may as well cache it
		//------------------------------------------------------------------------------------------
		$row = mysql_fetch_assoc($result);
		$row = $this->rmArray($row);					
		$this->cacheStore($model, $row);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a list of all tables in database and store in $this->tables
	//----------------------------------------------------------------------------------------------
	//returns: array of table names [array]

	function loadTables() {
		$this->tables = array();

		$result = $this->query("SHOW TABLES FROM " . $this->name);
		while ($row = $this->fetchAssoc($result)) {
			// we don't know the column name in advance, so we do this:
			foreach ($row as $table) { $this->tables[] = $table; } 
		}

		$this->tablesLoaded = true;
		return $this->tables;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if a table exists in the database
	//----------------------------------------------------------------------------------------------
	//arg: model - model name / name of a database table [string]
	//returns: true if there exists a table with the name given, false if not found [bool]

	function tableExists($model) {
		if (false == $this->tablesLoaded) { $this->loadTables(); }
		if (true == in_array($model, $this->tables)) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a blank serialized object from schema, with fields unset, magic members filled in
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - a table schema, as with dbSchema [array]
	//returns: copy of schema with magic fields filled in [array]

	function makeBlank($dbSchema) {
		global $kapenta, $user;

		$blank = array();
		foreach($dbSchema['fields'] as $fieldName => $fieldType) {
			$fieldType = strtoupper($fieldType);
			$blank[$fieldName] = '';

			if ($fieldType == 'DATETIME') {	$blank[$fieldName] = $this->datetime(); }
			if ($fieldType == 'TINYINT') { $blank[$fieldName] = '0'; }

			if ($fieldName == 'UID') { $blank[$fieldName] = $kapenta->createUID(); }
			if ($fieldName == 'createdBy') { $blank[$fieldName] = $user->UID; }
			if ($fieldName == 'editedBy') { $blank[$fieldName] = $user->UID; }
			if ($fieldName == 'editedOn') { $blank[$fieldName] = $this->datetime(); }
			if ($fieldName == 'createdOn') { $blank[$fieldName] = $this->datetime(); }
			if ($fieldName == 'revision') { $blank[$fieldName] = 0; }
		}
		return $blank;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a range of objects
	//----------------------------------------------------------------------------------------------
	//: if fields is empty string then all fields (*) are assumed
	//:	'conditions' is an array of conditions, eg 1 => role='admin', 2 => some='thing'
	//:	TODO: more security checks for sql inclusion, especially on conditons
	
	//arg: model - type of object / name of database table [string]
	//arg: fields - list of fields or empty string (SELECT clause) [string]
	//arg: conditions - array of conditions (WHERE clause) [array]
	//opt: by - field(s) to order by or empty string [string]
	//opt: limit - max record to return [int]
	//opt: offset - skip this many records from start [int]
	//returns: array of associative arrays (field -> value) with database markup removed [array]

	function loadRange($model, $fields, $conditions, $by = '', $limit = '', $offset = '') {
		$retVal = array();												//% return value [array]
		if (false == $this->tableExists($model)) { return $retVal; }	//	prevents SQL injection

		//------------------------------------------------------------------------------------------
		//	basic sql query to select all rows in table
		//------------------------------------------------------------------------------------------
		if ('' == $fields) { $fields = '*'; }
		$sql = "SELECT $fields FROM " . $model . " ";

		//------------------------------------------------------------------------------------------
		//	conditions to winnow it down by
		//------------------------------------------------------------------------------------------
		if ((is_array($conditions)) AND (count($conditions) > 0)) 
			{ $sql .= 'WHERE ' . implode(' AND ', $conditions) . ' '; }

		//------------------------------------------------------------------------------------------
		//	order by a particular field
		//------------------------------------------------------------------------------------------
		if (strlen($by) > 0) { $sql .= "ORDER BY $by "; }

		//------------------------------------------------------------------------------------------
		//	max n results
		//------------------------------------------------------------------------------------------
		if (($limit != false) AND ($limit != '')) 
			{ $limit = $this->addMarkup($limit); $sql .= "LIMIT $limit "; }
	
		//------------------------------------------------------------------------------------------
		//	starting from
		//------------------------------------------------------------------------------------------
		if (($offset != false) AND ($offset != '')) 
			{ $offset = $this->addMarkup($offset); $sql .= "OFFSET $offset "; }

		//------------------------------------------------------------------------------------------
		//	execute the query and return results as associative array
		//------------------------------------------------------------------------------------------
		$result = $this->query($sql);
		if (false === $result) { return $retVal; }

		while ($row = $this->fetchAssoc($result)) {
			foreach ($row as $fName => $fValue) { $row[$fName] = $this->removeMarkup($fValue); }
			$retVal[$row['UID']] = $row;
			if ('*' == $fields) { $this->cacheStore($model, $row); }
		}	
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//.	count a subset of objects
	//----------------------------------------------------------------------------------------------
	//: if fields is empty string then all fields (*) are assumed
	//:	'conditions' is an array of conditions, eg 1 => "ofGroup='admin'", 2 => "some='thing'"
	//:	TODO: more security checks for sql inclusion, especially on conditons
	//: TODO: consider returning false on error, why, why not...

	//arg: table - name of database table [string]
	//opt: conditions - array of conditions (WHERE clause) [array]
	//returns: number of records [int]

	function countRange($model, $conditions = '') {
		$model = strtolower($model);								// temporary
		if (false == $this->tableExists($model)) { return 0; }		//TODO: throw error?

		//------------------------------------------------------------------------------------------
		//	basic sql query to count UIDs
		//------------------------------------------------------------------------------------------
		$sql = "SELECT count(UID) as numRows FROM " . $model . " ";

		//------------------------------------------------------------------------------------------
		//	conditions to winnow it down by
		//------------------------------------------------------------------------------------------
		if ((is_array($conditions)) AND (count($conditions) > 0)) 
			{ $sql .= 'WHERE ' . implode(' AND ', $conditions) . ' '; }

		//------------------------------------------------------------------------------------------
		//	execute the query and return results as associative array
		//------------------------------------------------------------------------------------------
		$result = $this->query($sql);
		if (false === $result) { return 0; }
		while ($row = $this->fetchAssoc($result)) { return $row['numRows']; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//. format a date
	//----------------------------------------------------------------------------------------------
	//opt: timestamp - timestamp [int]
	//returns: same array with values unescaped (database markup removed) [string]
	
	function datetime($timestamp = false) {
		global $registry;
		if (false === $timestamp) { 
			$timestamp = time(); 
			$adjust = (int)$registry->get('kapenta.timedelta');
			if (0 != $adjust) { $timestamp = $timestamp + $adjust; }
		}
		$date = gmdate("Y-m-d H:i:s", $timestamp);
		return $date;
	}

	//----------------------------------------------------------------------------------------------
	//.	get table schema in Kapenta's dbSchema format (jagged array)	// DEPRECATED
	//----------------------------------------------------------------------------------------------
	//:note that nodiff is not generated, as this is not known by the DBMS
	//arg: tableName - name of a database table / model name [string]
	//returns: nested array describing database table [array]
	//TODO: remove this in favor of KSchema object

	function getSchema($tableName) {
		$tableName = strtolower($tableName);		// temporary
		if (false == $this->tableExists($tableName)) { return false; }

		//------------------------------------------------------------------------------------------
		//	create dbSchema array		TODO: not ideal, more error checking here
		//------------------------------------------------------------------------------------------
		$parts = explode('_', $tableName);

		$dbSchema = array(
			'module' => $parts[0],
			'model' => $tableName,
			'fields' => array(),
			'indices' => array(),
			'nodiff' => array()
		);

		//------------------------------------------------------------------------------------------
		//	add fields
		//------------------------------------------------------------------------------------------
		$sql = "describe " . $tableName;
		$result = $this->query($sql);
		while ($row = $this->fetchAssoc($result)) 
			{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

		//------------------------------------------------------------------------------------------
		//	add indices
		//------------------------------------------------------------------------------------------
		$sql = "show indexes from " . $tableName;
		$result = $this->query($sql);
		while ($row = $this->fetchAssoc($result)) 
			{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	check that a database schema is valid	// DEPRECATED, TODO: remove
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - a database table definition [array]
	//returns: true if valid, false if not [bool]
	//TODO: replace calls to this with KDBSchema->checkSchema

	function checkSchema($dbSchema) {
		if (false == is_array($dbSchema)) { return false; }
		if (false == array_key_exists('module', $dbSchema)) { return false; }
		if (false == array_key_exists('model', $dbSchema)) { return false; }
		if (false == array_key_exists('nodiff', $dbSchema)) { return false; }
		if (false == array_key_exists('fields', $dbSchema)) { return false; }

		if (false == is_array($dbSchema['fields'])) { return false; }
		if (false == array_key_exists('UID', $dbSchema['fields'])) { return false; }

		//TODO: more checks here (allowed field types, additional items, allowed table names, etc)
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	sanitize a value before using it in sql statement - to prevent SQL injection, etc
	//----------------------------------------------------------------------------------------------
	//arg: text - string to sanitize [string]
	//returns: escaped string [string]

	function addMarkup($text) {								// WHY?
		$text = str_replace('%', "[`|pc]", $text);			// wildcard characters in SQL
		$text = str_replace('_', "[`|us]", $text);			// ... 
		$text = str_replace(';', "[`|sc]", $text);			// used to construct SQL statements
		$text = str_replace("'", "[`|sq]", $text);			// ...
		$text = str_replace("\"", "[`|dq]", $text);			// ...
		$text = str_replace('<', "[`|lt]", $text);			// interference between nested XML
		$text = str_replace('>', "[`|gt]", $text);			// ...
		$text = str_replace("\t", "[`|tb]", $text);			// can cause mysql errors
		$text = str_replace('select', "[`|select]", $text);	// SQL statements  
		$text = str_replace('delete', "[`|delete]", $text);	// ...
		$text = str_replace('create', "[`|create]", $text);	// ...
		$text = str_replace('insert', "[`|insert]", $text);	// ...
		$text = str_replace('update', "[`|update]", $text);	// ...
		$text = str_replace('drop', "[`|drop]", $text);		// ...
		$text = str_replace('table', "[`|table]", $text);	// ...
		return $text;
	}


	//----------------------------------------------------------------------------------------------
	//.	remove sql markup
	//----------------------------------------------------------------------------------------------
	//arg: text - escaped string [string]
	//returns: unescaped string [string]

	function removeMarkup($text) {
		$text = str_replace("[`|pc]", '%', $text);
		$text = str_replace("[`|us]", '_', $text);
		$text = str_replace("[`|sc]", ';', $text);
		$text = str_replace("[`|sq]", "'", $text);
		$text = str_replace("[`|dq]", "\"", $text);
		$text = str_replace("[`|lt]", "<", $text);
		$text = str_replace("[`|gt]", ">", $text);
		$text = str_replace("[`|tb]", "\t", $text);
		$text = str_replace("[`|select]", 'select', $text);
		$text = str_replace("[`|delete]", 'delete', $text);
		$text = str_replace("[`|create]", 'create', $text);
		$text = str_replace("[`|insert]", 'insert', $text);
		$text = str_replace("[`|update]", 'update', $text);
		$text = str_replace("[`|drop]", 'drop', $text);
		$text = str_replace("[`|table]", 'table', $text);

		//------------------------------------------------------------------------------------------
		// legacy markup, from kapenta 1, remove these if not migrating old data
		//------------------------------------------------------------------------------------------

		$text = str_replace("[`|squote]", "'", $text);
		$text = str_replace("[`|quote]", "\"", $text);
		$text = str_replace("[`|semicolon]", ";", $text);

		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//. add sql markup to an array (no nested arrays)
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of field/value pairs [array]
	//returns: same array with values escaped (database markup added) [array]

	function amArray($ary) {
		$retVal = array();
		if (is_array($ary) == true) 
			{ foreach ($ary as $key => $val) { $retVal[$key] = $this->addMarkup($val); } }

		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//. remove sql markup from an array (no nested arrays)
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of field/value pairs [array]
	//returns: same array with values unescaped (database markup removed) [array]

	function rmArray($ary) {
		$retVal = array();
		if (is_array($ary) == true) 
			{ foreach ($ary as $key => $val) { $retVal[$key] = $this->removeMarkup($val); } }

		return $retVal;
	}
}

?>
