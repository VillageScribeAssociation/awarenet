<?

//--------------------------------------------------------------------------------------------------
//*	database driver (abstraction object) for SQLite3
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
//+ NOTE: additional database functionality is provided in sqliteadmin, this functionality was moved
//+	to this file because most user actions will not need it, and it keeps the default loaded
//+ codebase down.

class KDBDriver_SQLite {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $type = 'SQLite';		//_	database type [string]

	var $host = '';				//_	database host [string]
	var $user = '';				//_ database user name [string]
	var $pass = '';				//_	database password [string]
	var $name = '';				//_ database name [string]

	var $tables;				//_	set of tables in this database [array]
	var $tablesLoaded = false;	//_	set to true when list of tables has been loaded [bool]

	var $count = 0;				//_	number of queries executed over the life of this object [int]
	var $time = 0.00;			//_	total time spent in database queries, in seconds [float]

	var $lasterr = '';			//_	message describe last failed operation [string]
	var $lastquery = '';		//_	last query operation run on the database [string]

	var $dbh;					//_	database handle [object]
	var $connected = false;		//_	set to true when $this->dbh is set up [bool]
	var $inTransaction = false;	//_	set to true when in a transaction [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: dbHost - database host, default is 127.0.0.1 [string]
	//opt: dbUser - database user, default is root [string]
	//opt: dbPass - database password, no default [string]
	//opt: dbName - database name, default is 'kapenta' [string]

	function KDBDriver_SQLite() {
		global $kapenta;			// perhaps get these from $kapenta

		$this->host = 'localhost';
		$this->user = 'kapenta';
		$this->pass = '';
		$this->name = $kapenta->registry->get('db.sqlite.name');

		//	recovery / backup store of this information
		if ('' == $this->name) {
			$this->name = $kapenta->registry->get('kapenta.db.name');
			if ('' != $this->name) { $kapenta->registry->set('db.sqlite.name', $this->name); }
		}

		$this->tables = array();
	}

	//==============================================================================================
	//	WRAPPER METHODS - for MySQL
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	execute a query, return handle
	//----------------------------------------------------------------------------------------------
	//arg: query - a SQL query [string]
	//returns: handle to query result or false on failure [int][bool]

	function query($query) {
		global $kapenta, $session, $page, $registry;

		$connect = false;							//%	database connection handle [int]
		$selected = false;							//%	database selection [bool]
		$result = false;							//%	recordset handle [int]

		$this->lasterr = '';						//	clear any previous error message
		$this->lastquery = $query;					//	record this query for debugging

		$this->count++;								//	increment query count for this page view
		$startTime = microtime(true);				//	record start time

		if ((true == isset($page)) && (true == $page->logDebug)) {
			$page->logDebugItem('query', $query);
		}

		//------------------------------------------------------------------------------------------
		// open database file on first query
		//------------------------------------------------------------------------------------------

		if (false == $this->connected) {
			try {

				$this->dbh = new PDO('sqlite:' . $this->name . '.sq3');
				$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->connected = true;

			} catch(PDOException  $e) {

				$msg = "SQLite PDO Connection failed: " . $e->getMessage();
				if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
				else { echo $msg . "<br/>\n"; }
				return false;

			}

		}

		//------------------------------------------------------------------------------------------
		//	prepare the query
		//------------------------------------------------------------------------------------------
		//echo "SQLite: $query <br/>\n";
		try { $sth = $this->dbh->prepare($query); }
		catch(PDOException $e) {
			$msg = "Failed to prepare SQL Statement: $query<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
			$this->lasterr = $msg;
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	execute the query
		//------------------------------------------------------------------------------------------
		try { $check = $sth->execute(); }
		catch(PDOException $e) {
			$msg = "Failed to execute SQL Statement: $query<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
			$this->lasterr = $msg;
			return false;
		}

		if (false === $check) {
			$msg = "Could not execute database query:<br/>" . $query . "<hr/><br/>" . mysql_error();
			if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
			$this->lasterr = $msg;
			return false;
		}

		//------------------------------------------------------------------------------------------
		// log this query
		//------------------------------------------------------------------------------------------
		$endTime = microtime(true);
		$diff = $endTime - $startTime;
		$this->time += $diff;

		if ($diff > 1) {
			$msg = $diff . ' - ' . $query;
			$kapenta->logEvent('db-slow', 'sqlite', 'query', $msg);
		}

		return $sth;		// result object
	}

	//----------------------------------------------------------------------------------------------
	//.	begin a transaction
	//----------------------------------------------------------------------------------------------
	//retuns: true if transaction started, false if one is already in progress [bool]

	function transactionStart() {
		if (false == $this->connected) { return false; }
		if (true == $this->inTransaction) { return false; }
		$this->query('BEGIN TRANSACTION;');
		$this->inTransaction = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	end a transaction
	//----------------------------------------------------------------------------------------------
	//retuns: true if transaction ended, false if transaction not in progress [bool]

	function transactionEnd() {
		if (false == $this->connected) { return false; }
		if (false == $this->inTransaction) { return false; }
		$this->query('COMMIT TRANSACTION;');
		$this->inTransaction = false;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a row from a recordset,
	//----------------------------------------------------------------------------------------------
	//arg: handle - handle to a MySQL query result [int]
	//returns: associative array of field names and values, database markup not removed [array]

	function fetchAssoc($handle) { 
		if (false === $handle) { return false; }
		$row = $handle->fetch();
		return $row;
	}

	//----------------------------------------------------------------------------------------------
	//.	get number of rows in recordset
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: note that this does NOT return the number or rows underSQLite
	//arg: handle - handle to a MySQL query result [int]
	//returns: number of rows or false on failure [int] [bool]

	function numRows($handle) {
		global $session;

		$session->msgAdmin("DEPRECATED: \$db->numRows() does not work on SQLite");

		if (false === $handle) { return false; }
		$num = 0;
		try { $num = $handle->rowCount(); }
		catch (PDOException $e) {
			$msg = "Failed to count rows in query:<br/>\n" . $e->getMessage();
			if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
			else { echo $msg . "<br/>\n"; }
		}
		return $num;
	}

	//==============================================================================================
	//	SCHEMA BASED IO - database calls specifying table schema
	//==============================================================================================

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
		global $kapenta;
		global $page;

		$model = strtolower($dbSchema['model']);
		if (false == $this->tableExists($model)) { return false; }

		//------------------------------------------------------------------------------------------
		//	check the cache for this object
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == $kapenta->cacheHas($cacheKey)) {
			if ((true == isset($page)) && (true == $page->logDebug)) {
				$page->logDebugItem('dbLoad', 'cache hit: ' . $model . '::' . $UID);
			}
			return unserialize($kapenta->cacheGet($cacheKey));
		} else {
			if ((true == isset($page)) && (true == $page->logDebug)) {
				$page->logDebugItem('dbLoad', 'cache miss: ' . $model . '::' . $UID);
			}
		}

		//------------------------------------------------------------------------------------------
		//	object is not in cache, try load from database
		//------------------------------------------------------------------------------------------

		$sql = "SELECT * FROM " . $model . " where UID='" . $this->addMarkup($UID) . "'";
		$recordSet = $this->query($sql);
		if (false == $recordSet) { return false; }
		while ($record = $recordSet->fetch()) {
			//--------------------------------------------------------------------------------------
			// object found - strip database markup, store in cache and return
			//--------------------------------------------------------------------------------------
			$objAry = array();	
			foreach($record as $fName => $fVal) { $objAry[$fName] = $this->removeMarkup($fVal); }
			$kapenta->cacheSet($cacheKey, serialize($objAry));

			if (true == array_key_exists('alias', $objAry)) {
				$aliasKey = 'alias::' . $model . '::' . strtolower($objAry['alias']);
				$kapenta->cacheSet($aliasKey, $objAry['UID']);
			}
			return $objAry;
		}

		if ((true == isset($page)) && (true == $page->logDebug)) {
			$page->logDebugItem('dbLoad', "no such object: $model::$UID");
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	load a record from a table supporting object Aliases, return associative array
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of an object [string]
	//arg: dbSchema - database table definition [array]
	//returns: return associative array of field names and values or false on failure [array] [bool]

	function loadAlias($raUID, $dbSchema) {
		global $kapenta;
		global $page;
		global $session;

		$model = strtolower($dbSchema['model']);

		if (false == $this->tableExists($model)) { return false; }

		if (true == $kapenta->mcEnabled) {
			//--------------------------------------------------------------------------------------
			//	try load from cache by UID
			//--------------------------------------------------------------------------------------
			$cacheKey = $model . '::' . $raUID;
			if (true == $kapenta->cacheHas($cacheKey)) { 
				if ((true == isset($page)) && (true == $page->logDebug)) {
					$page->logDebugItem('dbLoad', "cache hit: $cacheKey (loadAlias)");
				}
				$objStr = $kapenta->cacheGet($cacheKey);
				$objAry = unserialize($objStr);
				return $objAry;
			}

			//--------------------------------------------------------------------------------------
			//	try load from cache by canonical alias
			//--------------------------------------------------------------------------------------
			$aliasKey = 'alias::' . $model . '::' . strtolower($raUID);
			if (true == $kapenta->cacheHas($aliasKey)) {
				$UID = $kapenta->cacheGet($aliasKey);
				if ((true == isset($page)) && (true == $page->logDebug)) {
					$page->logDebugItem('dbLoad', "alias hit: $model::$raUID => $UID");
				}
				return $this->load($UID, $dbSchema);
			}

			//------------------------------------------------------------------------------------------
			//	try load from cache by an alternate alias
			//------------------------------------------------------------------------------------------
			$redirectKey = 'aliasalt::' . $model . '::' . strtolower($raUID);
			if (true == $kapenta->cacheHas($redirectKey)) {
				$canonical = $kapenta->cacheGet($redirectKey);
				if (strtolower($canonical) == strtolower($raUID)) {
					echo "Error: circular reference in alias cache.<br/>\n";
					return array();
				}
				return $this->loadAlias($canonical, $dbSchema);
			}
		}

		//------------------------------------------------------------------------------------------
		//	not found in cache, try load directly
		//------------------------------------------------------------------------------------------
		if ((true == isset($page)) && (true == $page->logDebug)) {
			$page->logDebugItem('dbLoad', 'cache miss: ' . $model . '::' . $raUID . ' (loadAlias)');
		}

		if (true == $this->objectExists($model, $raUID)) { return $this->load($raUID, $dbSchema); }

		//------------------------------------------------------------------------------------------
		//	not found in cache, not a UID, try lookup in aliases_alias table
		//------------------------------------------------------------------------------------------
		if (true == $this->tableExists('aliases_alias')) {
			$sql = ''
			 . "SELECT * FROM aliases_alias "
			 . "WHERE refModel='" . $this->addMarkup($model) . "' "
			 . "AND aliaslc='" . $this->addMarkup(strtolower($raUID)) . "'";

			$result = $this->query($sql);
			
			while ($row = $this->fetchAssoc($result)) {
				$objAry = $this->rmArray($row);
				$refUID = $this->removeMarkup($objAry['refUID']);

				if (true == $this->objectExists($model, $refUID)) {

					//------------------------------------------------------------------------------
					//	reference is valid, cache this for next time
					//------------------------------------------------------------------------------
					if (
						(true == $kapenta->mcEnabled) &&
						(true == array_key_exists('UID', $objAry)) &&
						(true == array_key_exists('alias', $objAry))
					) {
						// canonical alias
						$aliasKey = 'alias::' . $model . '::' . strtolower($objAry['alias']);
						$kapenta->cacheSet($aliasKey, $objAry['UID']);
						
						// redirect, if not canonical
						if (strtolower($raUID) != strtolower($objAry['alias'])) {
							$redirectKey = 'aliasalt::' . $model . '::' . strtolower($raUID);
							$kapenta->cacheSet($redirectKey, $objAry['alias']);
						}
					}

					$objAry = $this->load($refUID, $dbSchema);
				}
			}

		}

		//------------------------------------------------------------------------------------------
		//	not found in cache or alises table, try load from object table
		//------------------------------------------------------------------------------------------
		if (true == array_key_exists('alias', $dbSchema['fields'])) {
			//TODO: this query os O(n) because of the LOWER() function, make it faster
			$conditions = array("LOWER(alias)='" . $this->addMarkup($raUID) . "'");
			$range = $this->loadRange($model, '*', $conditions, 'editedOn', '1');

			foreach ($range as $objAry) {
				//----------------------------------------------------------------------------------
				// object found - store in cache and return
				//----------------------------------------------------------------------------------
				$cacheKey = $model . '::' . $objAry['UID'];
				$aliasKey = 'alias::' . $model . '::' . strtolower($objAry['alias']);
				$kapenta->cacheSet($cacheKey, serialize($objAry));
				$kapenta->cacheSet($aliasKey, $objAry['UID']);

				if (strtolower($objAry['alias']) != strtolower($raUID)) {
					$redirectKey = 'aliasalt::' . $model . '::' . strtolower($raUID);
					$kapenta->cacheSet($redirectKey, $objAry['alias']);
				}

				//----------------------------------------------------------------------------------
				// update the aliases table here
				//----------------------------------------------------------------------------------
				//TODO: this

				return $objAry;
			}
		}

		//------------------------------------------------------------------------------------------
		//	out of options
		//------------------------------------------------------------------------------------------
		if ((true == isset($page)) && (true == $page->logDebug)) {
			$page->logDebugItem('dbLoad', 'no such object: ' . $model . '::' . $raUID);
		}

		return false;
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
		global $user;
		global $revisions;	
		global $session;
		global $kapenta;

		$changes = array();								//%	fields which have changed [dict]
		$dirty = false;									//%	if changes need to be saved [bool]
		$this->lasterr = '';

		//------------------------------------------------------------------------------------------
		//	check arguments
		//------------------------------------------------------------------------------------------

		if (false == is_array($data)) {
			$this->lasterr = 'No object array given.';
			return false;								// must include and object to save
		}

		if ((false == array_key_exists('UID', $data)) || (strlen(trim($data['UID'])) < 4)) {
			$this->lasterr = 'No valid UID for given object.';
			return false;								// object must have a UID, 4 chars or more
		}

		$dbSchema['model'] = strtolower($dbSchema['model']);				// temporary

		if (false == $this->tableExists($dbSchema['model'])) {
			$this->lasterr = 'Table does not exist.';
			return false;
		}

		//	adds support for primary keys for tables which must have both UID and Pri Key
		if (false == array_key_exists('prikey', $dbSchema)) { $dbSchema['prikey'] = ''; }

		// do not save objects which have been deleted
		if (true == $revisions->isDeleted($dbSchema['model'], $data['UID'])) {
			$this->lasterr = 'This object is deleted, must be undeleted before saving.';
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	set editedBy, editedOn if present in schema
		//------------------------------------------------------------------------------------------
		if ((true == $setdefaults) && (true == isset($user))) {
			if (true == array_key_exists('editedBy', $dbSchema['fields'])) {
				$data['editedBy'] = $user->UID;
			}
			if (true == array_key_exists('editedOn', $dbSchema['fields'])) {
				$data['editedOn'] = $this->datetime();
			}
		}

		//------------------------------------------------------------------------------------------
		//	try load previous version of this record, replace any previously cached version
		//------------------------------------------------------------------------------------------
		$current = $this->load($data['UID'], $dbSchema);			//%	previous version [array]
		$cacheKey = $dbSchema['model'] . '::' . $data['UID'];		//%	cache identifier
		$kapenta->cacheSet($cacheKey, serialize($data));				

		if (true == array_key_exists('alias', $data)) {
			$aliasKey = 'alias::' . $dbSchema['model'] . '::' . strtolower($data['alias']);
			$kapenta->cacheSet($aliasKey, $data['UID']);
		}

		//------------------------------------------------------------------------------------------
		//	if no previous version exists, save this one
		//------------------------------------------------------------------------------------------
		if (false == $current) {
			$dirty = true;				//	add everything as a new revision, so we can revert
			$changes = $data;			//	to this first version.

			$newFields = array();
			foreach ($dbSchema['fields'] as $fName => $fType) {
				$value = '';
				if (true == array_key_exists($fName, $data))
					{ $value = $this->addMarkup($data[$fName]); }		// prevent SQL injection
				if (true == $this->quoteType($fType)) 
					{ $value = "\"" . $value . "\""; }					// quote string values

				if ($fName !== $dbSchema['prikey']) { $newFields[$fName] = $value; }
			}

			// assemble the query
			$sql = ''
			 . "INSERT INTO ". $dbSchema['model']
			 . " (" . implode(', ', array_keys($newFields)) . ")"
			 . " VALUES"
			 . " (" . implode(', ', $newFields) . ");";

			$result = $this->query($sql);								// run it...
			if (false === $result) {
				$this->lasterr = "Insert operation failed:<br/>\n" . $this->lasterr;
				return false;											// could not save
			}

		} else {
			//--------------------------------------------------------------------------------------
			//	pervious version does exist, find if/where it differs from this one
			//--------------------------------------------------------------------------------------
			foreach($current as $fName => $fVal) {
			    if (
					(true == array_key_exists($fName, $data)) &&			//	field exists
					($fVal != $data[$fName]) &&								//	and data has changed
					($fName != $dbSchema['prikey'])							//	and not the prikey
				) {	
					$changes[$fName] = $this->addMarkup($data[$fName]); 	//	this has changed

					if (
						(true == array_key_exists('nodiff', $dbSchema)) &&
						(true == is_array($dbSchema['nodiff'])) &&
						(false == array_key_exists($fName, $dbSchema['nodiff']))
					) {	$dirty = true; }

			    }
			}

			if (0 == count($changes)) { return true; }						// nothing to do

			//--------------------------------------------------------------------------------------
			//	make changes to stored record
			//--------------------------------------------------------------------------------------
			$setter = array();
			foreach($changes as $fName => $fVal) {
				if (
					(true == array_key_exists($fName, $dbSchema['fields'])) &&
					(true == $this->quoteType($dbSchema['fields'][$fName])) 
				) { $fVal = "\"" . $fVal . "\""; }

				$setter[$fName] = "`" . $fName . "`" . '=' . $fVal;
			}

			$sql = ''
			 . "UPDATE " . $dbSchema['model'] 
			 . " SET " . implode(', ', $setter)
			 . " WHERE UID='" . $this->addMarkup($data['UID']) . "';";

			$result = $this->query($sql);

			if (false === $result) {
				$msg = 'could not update record: ' . $dbSchema['model'] . " " . $data['UID'];
				$session->msgAdmin($msg, 'bad');
				$this->lasterr = "Update operation failed:<br/>\n" . $this->lasterr;
				return false;
			}
		}

		//------------------------------------------------------------------------------------------
		//	allow other modules to respond to this event
		//------------------------------------------------------------------------------------------		
		$args = array(
			'module' => $dbSchema['module'],
			'model' => $dbSchema['model'],
			'UID' => $data['UID'],
			'data' => $data,
			'changes' => $changes,
			'dbSchema' => $dbSchema,
			'revision' => ($revision ? 'yes' : 'no'),
			'broadcast' => ($broadcast ? 'yes' : 'no')
		);

		$kapenta->raiseEvent('*', 'object_updated', $args);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete an object (if it exists)
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / name of database table [string]
	//arg: dbSchema - Database table schema [array]
	//returns: true on success, false on failure [bool]

	function delete($UID, $dbSchema) {
		global $kapenta, $revisions, $session;

		$this->lasterr = '';								//	clear any previous error message

		$module = $dbSchema['module'];
		$model = strtolower($dbSchema['model']);			//	TODO: remove strtolower when safe

		// this also checks that table exists:
		if (false == $this->objectExists($model, $UID)) {
			$this->lasterr = '';
			return false;
		}
		
		if (('users_user' == $model) || ('schools_school' == $model)) {
			$this->lasterr = 'Refusing to delete protected object.';
			return false;									//	SPECIAL CASE, security
		}

		$isShared = $this->isShared($model, $UID);

		$objAry = $this->load($UID, $dbSchema);

		if ((false == is_array($objAry)) || (0 == count($objAry))) {
			$this->lasterr = 'Object not available for deletetion.';
			return false;									//	nothing to do
		}

		//------------------------------------------------------------------------------------------
		//	remove from original table
		//------------------------------------------------------------------------------------------
		$sql = "DELETE FROM " . $model . " WHERE UID='" . $this->addMarkup($UID) . "'";
		$check = $this->query($sql);

		if (false == $check) {
			$this->lasterr = "DELETE operation failed:<br/>\n" . $this->lasterr;
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	remove from cache
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == $kapenta->cacheHas($cacheKey)) {
			$kapenta->cacheDelete($cacheKey);
			if (true == array_key_exists('alias', $objAry)) { 
				$aliasKey = 'alias::' . $model . '::' . strtolower($objAry['alias']);
				$kapenta->cacheDelete($aliasKey);
			}
		}

		//------------------------------------------------------------------------------------------
		//	send event to any modules which may need to do something about this
		//------------------------------------------------------------------------------------------
		$detail = array(
			'module' => $module,
			'model' => $model,
			'UID' => $UID, 
			'data' => $objAry,
			'dbSchema' => $dbSchema,
			'isShared' => $isShared
		);

		$kapenta->raiseEvent('*', 'object_deleted', $detail);
		//echo "deleting object... $module $model $UID <br/>\n";
		return true;
	}

	//==============================================================================================
	//	DEPRECATED
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	update a record field without causing resync, strings only
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: todo - find any calls to this and remove
	//arg: model - model name / name of database table [string]
	//arg: UID - UID of a record [string]
	//arg: field - field name [string]
	//arg: value - field value [string]
	//: this is used where an object should change only locally and changes not sent to peer servers

	function updateQuiet($model, $UID, $field, $value) {	
		global $session;
		$session->msg("DEPRECATED: db::updateQuiet($model, $UID, $field, $value)", 'warn');
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

	//==============================================================================================
	//	RANGE METHODS - for handling sets of serialized objects where n is small
	//==============================================================================================

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

	function loadRange($model, $fields ='*', $conditions ='', $by ='', $limit ='', $offset ='') {
		global $kapenta;

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

			//--------------------------------------------------------------------------------------
			//	cache any complete records we have loaded
			//--------------------------------------------------------------------------------------
			if (('*' == $fields) && (true == array_key_exists('UID', $row))) {
				$cacheKey = $model . '::' . $row['UID'];

				$kapenta->cacheSet($cacheKey, serialize($row));

				if (true == array_key_exists('alias', $row)) {
					$aliasKey = 'alias::' . $model . '::' . strtolower($row['alias']);
					$kapenta->cacheSet($aliasKey, $row['UID']);
				}
			}

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

	//==============================================================================================
	//	TABLE METHODS - list, create, etc
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	make a list of all tables in database and store in $this->tables
	//----------------------------------------------------------------------------------------------
	//;	this is deprecated in favor of listTables()
	//;	SQLite3 does not support SHOW TABLES as of 2012-07-24
	//returns: array of table names [array]

	function loadTables() {
		global $kapenta;

		$this->tables = array();
		
		//------------------------------------------------------------------------------------------
		//	try load from memcached
		//------------------------------------------------------------------------------------------
		$cacheKey = 'db::sqlite::tables';
		if ((true == $kapenta->mcEnabled) && (true == $kapenta->cacheHas($cacheKey))) {
			$this->tables = explode('|', $kapenta->cacheGet($cacheKey));
		}

		//------------------------------------------------------------------------------------------
		//	try load from database master
		//------------------------------------------------------------------------------------------
		if (0 == count($this->tables)) {
			//	No PRAGMA in current version to do this, strix 2012-08-01
			$result = $this->query("SELECT * FROM sqlite_master WHERE type='table';");
			while ($row = $this->fetchAssoc($result)) { $this->tables[] = $row['name'];}

			//	keep this for next time
			if (true == $kapenta->mcEnabled) {
				$kapenta->cacheSet($cacheKey, implode('|', $this->tables));
			}
		}

		if (0 == count($this->tables)) { return false; }

		$this->tablesLoaded = true;
		return $this->tables;
	}

	//----------------------------------------------------------------------------------------------
	//.	alias if loadTables (preferred)
	//----------------------------------------------------------------------------------------------

	function listTables() { return $this->loadTables(); }

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
		global $kapenta, $user, $session;

		$blank = array();
		foreach($dbSchema['fields'] as $fieldName => $fieldType) {
			$fieldType = strtoupper($fieldType);
			$blank[$fieldName] = '';

			if ('UID' == $fieldName) {
				$continue = true;

				while(true == $continue) {
					$newUID = $kapenta->createUID();

					if (false == $this->objectExists($dbSchema['model'], $newUID)) {
						$blank[$fieldName] = $newUID;
						$continue = false;
					} else {
						//	this should never happen
						echo "UID collision in table: " . $dbSchema['model'] . "::$newUID<br/>\n";
						$continue = false;
					}
				}
			}

			if ('DATETIME' == $fieldType) {	$blank[$fieldName] = $this->datetime(); }
			if ('TINYINT' == $fieldType) { $blank[$fieldName] = '0'; }

			if (true == isset($user)) {
				if ('createdBy' == $fieldName) { $blank[$fieldName] = $user->UID; }
				if ('editedBy' == $fieldName) { $blank[$fieldName] = $user->UID; }
			}		

			if ('editedOn' == $fieldName) { $blank[$fieldName] = $this->datetime(); }
			if ('createdOn' == $fieldName) { $blank[$fieldName] = $this->datetime(); }
			if ('revision' == $fieldName) { $blank[$fieldName] = 0; }
			if ('shared' == $fieldName) { $blank[$fieldName] = 'yes'; }
		}

		return $blank;
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
		$sql = "PRAGMA table_info($tableName);";
		$result = $this->query($sql);
		while ($row = $this->fetchAssoc($result)) {
			$dbSchema['fields'][$row['name']] = strtoupper($row['type']);
		}

		//------------------------------------------------------------------------------------------
		//	add indices
		//------------------------------------------------------------------------------------------

		/* -- alternative form, less efficient,use if PRAGMA is dropped in future
		$sql = ''
		 . "SELECT * FROM `sqlite_master`"
		 . " WHERE `type`='index'"
		 . " AND `tbl_name`='" . $tableName . "'";
		*/		

		$sql = "PRAGMA index_list($tableName);";

		$result = $this->query($sql);
		while ($row = $this->fetchAssoc($result)) {
			$colName = str_replace('idx' . strtolower($tableName), '', $row['name']);
			$dbSchema['indices'][$colName] = '*';
		}

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
		if (false == array_key_exists('fields', $dbSchema)) { return false; }

		if (
			(false == array_key_exists('nodiff', $dbSchema)) &&
			(false == array_key_exists('diff', $dbSchema))
		) { return false; }

		if (false == is_array($dbSchema['fields'])) { return false; }
		if (false == array_key_exists('UID', $dbSchema['fields'])) { return false; }

		//TODO: more checks here (allowed field types, additional items, allowed table names, etc)
		return true;
	}

	//==============================================================================================
	//	UTILITY METHODS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	determine is a field should be quoted in SQL queries, given its type
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
		global $kapenta;

		$model = strtolower($model);								// temporary

		if (('' == trim($model)) || ('' == trim($UID))) { return false; }

		//------------------------------------------------------------------------------------------
		//	if we already have this object in the cache, assume it exists
		//------------------------------------------------------------------------------------------
		$cacheKey = $model . '::' . $UID;
		if (true == $kapenta->cacheHas($cacheKey)) { return true; }

		//------------------------------------------------------------------------------------------
		//	not in cache, try database
		//------------------------------------------------------------------------------------------
		if (false == $this->tableExists($model)) { return false; }	// prevent SQL injection
		$sql = "SELECT * FROM `$model` WHERE `UID`='" . $this->addMarkup($UID) . "';";		
		$result = $this->query($sql);
		if (false === $result) { return false; } 					// bad table name?

		//------------------------------------------------------------------------------------------
		// object exists, may as well cache it
		//------------------------------------------------------------------------------------------
		while ($row = $result->fetch()) {
			$row = $result->fetch();
			$row = $this->rmArray($row);

			if (true == array_key_exists('UID', $row)) {
				$cacheKey = $model . '::' . $row['UID'];
				$kapenta->cacheSet($cacheKey, serialize($row));
				if (true == array_key_exists('alias', $row)) {
					$aliasKey = 'alias::' . $model . '::' . strtolower($row['alias']);
					$cacheSet($aliasKey, $row['UID']);
				}
			}

			return true;
		}
		return false;
	}

	//==============================================================================================
	//	NON-SCHEMA IO - are now wrappers 
	//==============================================================================================
	//TODO: consider whether the get and store methods are really necessary, remove if possible
	// but leave the serialization to and from XML

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database, serialized as an array
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / table name [string]
	//arg: UID - UID of an object in this table [string]
	//returns: object serialized as an array, empty array on failure [array]

	function getObject($model, $UID) {
		global $kapenta;
		if (false == $this->tableExists($model)) { return $item; }

		$cacheKey = $model . '::' . $UID;				//%	memcache key [string]
		$item = array();								//%	return value [dict]

		//------------------------------------------------------------------------------------------
		//	first try to load from cache
		//------------------------------------------------------------------------------------------
		if ((true == $kapenta->mcEnabled) && (true == $kapenta->cacheHas($cacheKey))) {
			return unserialize($kapenta->cacheGet($cacheKey));
		}

		//------------------------------------------------------------------------------------------
		//	load from database
		//------------------------------------------------------------------------------------------
		$conditions = array("UID='" . $this->addMarkup($UID) . "'");
		$range = $this->loadRange($model, '*', $conditions, 'createdOn', '1');

		foreach($range as $item) {
			//------------------------------------------------------------------------------------------
			//	memcache the object for next time
			//------------------------------------------------------------------------------------------
			if (true == $kapenta->mcEnabled) {
				$kapenta->cacheSet($cacheKey, serialize($item));
				if (true == array_key_exists('alias', $item)) {
					$aliasKey = 'alias::' . $model . '::' . strtolower($item['alias']);
					$kapenta->cacheSet($aliasKey, $item['UID']);
				}
			}
		}

		return $item;
	}

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database, serialized as xml
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / table name [string]
	//arg: UID - UID of an object in this table [string]
	//returns: object serialized as xml, empty string on failure [string]

	function getObjectXml($model, $UID) {
		$item = $this->getObject($model, $UID);
		if (0 == count($item)) { return ''; }
		$xml = "<kobject type=\"" . $model . "\">\n";
		foreach($item as $key => $value) { $xml .= "\t<$key>". base64_encode($value) ."</$key>\n"; }
		$xml .= "</kobject>";
		return $xml;
	}

	//----------------------------------------------------------------------------------------------
	//.	store an object in the database, from XML serializarion
	//----------------------------------------------------------------------------------------------
	//opt: setdefaults - if true will automatically modify editedBy, editedOn, etc [bool]
	//opt: broadcast - if true, then pass to other peers [bool]
	//opt: revision - if true, then keep any revision data [bool]
	//returns: true on success, false on failure [bool]

	function storeObjectXml($xml, $setdefaults = true, $broadcast = true, $revision = true) {
		$this->lasterr = '';
		$objAry = $this->objectXmlToArray($xml);
		if (0 == count($objAry)) {
			$this->lasterr .= "Failed to parse XML.<br/>\n";
			return false;
		}

		$dbSchema = $this->getSchema($objAry['model']);
		if (false == is_array($dbSchema)) {
			$this->lasterr = 'Could not load db schema.';
			return false;
		}

		$check = $this->save($objAry['fields'], $dbSchema, $setdefaults, $broadcast, $revision);
		
		if (false == $check) { $this->lasterr = 'Could not save object.'; }
		return $check;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert an object serialized as XML to an array
	//----------------------------------------------------------------------------------------------
	//arg: xml - as produced by getObjectXml [string]
	//returns: array of model, fields on success, empty array on failure [array]
	
	function objectXmlToArray($xml) {
		$objAry = array(); 					//%	return value [array]
		$tableName = '';

		$xd = new KXmlDocument($xml);
		$root = $xd->getEntity(1);
		$data = $xd->getChildren2d();

		if (false == array_key_exists('attributes', $root)) { return $objAry; }
		if (false == array_key_exists('type', $root['attributes'])) { return $objAry; }

		$tableName = $root['attributes']['type'];
		$tableName = str_replace("\"", '', trim($tableName));

		if (false == $this->tableExists($tableName)) { return $objAry; }

		foreach($data as $field => $value) { $data[$field] = base64_decode($value); }
		$objAry['model'] = $tableName;
		$objAry['fields'] = $data;		

		return $objAry;
	}

	//----------------------------------------------------------------------------------------------
	//.	check if an object is shared / replicated with other peers
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / table name [string]
	//arg: UID - unique ID of object [string]
	//returns: true if shared, false if not [bool]

	function isShared($model, $UID) {
		$shared = true;									//%	return value [bool]
		$objAry = $this->getObject($model, $UID);		//%	object serialized as array [array]
		if (0 == count($objAry)) { $shared = false; }	//	object does not exist [string]

		// in cases where share staus is explicitly noted
		if (true == array_key_exists('shared', $objAry)) {
			if ('no' == $objAry['shared']) { return false; }
			return $shared;
		}

		// in cases where object inherits share status from some owner object
		if (
			(true == array_key_exists('refUID', $objAry)) &&
			(true == array_key_exists('refModel', $objAry))
		) {
			if ($objAry['refUID'] != $UID) {
				//TODO: better check for circular references
				$shared = $this->isShared($objAry['refModel'], $objAry['refUID']);
			}
		}

		// everything is shared by default
		return $shared;
	}

	//----------------------------------------------------------------------------------------------
	//. format a date (deprecated)
	//----------------------------------------------------------------------------------------------
	//opt: timestamp - timestamp [int]
	//returns: same array with values unescaped (database markup removed) [string]
	//; for coherence sake this has been moved to ksystem
	//; TODO: find and remove references to db->datetime();

	function datetime($timestamp = 0) {
		global $kapenta;		
		return $kapenta->datetime($timestamp);
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

		/*
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
		*/

		$replace = array(
			"[`|pc]" => '%',
			"[`|us]" => '_',
			"[`|sc]" => ';',
			"[`|sq]" => "'",
			"[`|dq]" => "\"",
			"[`|lt]" => "<",
			"[`|gt]" => ">",
			"[`|tb]" => "\t",
			"[`|select]" => 'select',
			"[`|delete]" => 'delete',
			"[`|create]" => 'create',
			"[`|insert]" => 'insert',
			"[`|update]" => 'update',
			"[`|drop]" =>'drop',
			"[`|table]" => 'table'
		);

		$text = str_replace(array_keys($replace), array_values($replace), $text);

		//------------------------------------------------------------------------------------------
		// legacy markup, from kapenta 1, remove these if not migrating old data
		//------------------------------------------------------------------------------------------

		/*
		$text = str_replace("[`|squote]", "'", $text);
		$text = str_replace("[`|quote]", "\"", $text);
		$text = str_replace("[`|semicolon]", ";", $text);
		*/

		return $text;
	}

	//----------------------------------------------------------------------------------------------
	//. add sql markup to an array (no nested arrays)
	//----------------------------------------------------------------------------------------------
	//arg: ary - associative array of field/value pairs [array]
	//returns: same array with values escaped (database markup added) [array]

	function amArray($ary) {
		$retVal = array();
		if (true == is_array($ary)) 
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
		if (false == is_array($ary)) { return $retVal; }
		foreach ($ary as $key => $val) { $retVal[$key] = $this->removeMarkup($val); }
		return $retVal;
	}

	//----------------------------------------------------------------------------------------------
	//. serialize a dict of fields for transport over p2p
	//----------------------------------------------------------------------------------------------
	//arg: fields - dict of field name => value [array]
	//returns: p2p formatted 7bit safe string [string]

	function serialize($fields) {
		$fields64 = '';
		foreach($fields as $field => $value) {
			if (false == is_numeric($field)) {
				$fields64 .= "\t" . $field . ':' . base64_encode($value) . "\n";
			}
		}
		return $fields64;
	}

	//----------------------------------------------------------------------------------------------
	//. unserialize a dict of fields received from p2p
	//----------------------------------------------------------------------------------------------
	//returns: fields64 - p2p formatted 7bit safe string representing a set of fields [string]
	//returns: dict of field name => value [array]

	function unserialize($fields64) {
		$fields = array();
		$lines = explode("\n", $fields64);
		foreach($lines as $line) {
			if ('' != trim($line)) {
				$parts = explode(':', $line, 2);
				if (2 == count($parts)) { $fields[trim($parts[0])] = base64_decode($parts[1]); }
			}
		}
		return $fields;
	}

}

?>
