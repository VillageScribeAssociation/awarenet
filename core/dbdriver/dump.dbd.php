<?

//--------------------------------------------------------------------------------------------------
//*	database driver (abstraction object) for dumping text output
//--------------------------------------------------------------------------------------------------
//+ Table schema are passed as nested associative arrays:
//+
//+  'table' -> string, name of table
//+  'fields' -> array of fieldname -> type, where type is MySQL type
//+  'indices' -> array of fieldname -> size (index name is derived from fieldname)
//+	 'nodiff' -> array of field names which are not versioned (eg, hitcount)
//+
//+ NOTE: additional database functionality is provided in dumpadmin, this functionality was moved
//+	to this file because most user actions will not need it, and it keeps the default loaded
//+ codebase down.

class KDBDriver_Dump {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $name = '';				//_ database name [string]

	var $count = 0;				//_	number of queries executed over the life of this object [int]
	var $time = 0.00;			//_	total time spent in database queries, in seconds [float]

	var $lasterr = '';			//_	message describe last failed operation [string]
	var $lastquery = '';		//_	last query operation run on the database [string]

	var $dbh;					//_	file handle [object]
	var $connected = false;		//_	set to true when $this->dbh is set up [bool]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: dbHost - database host, default is 127.0.0.1 [string]
	//opt: dbUser - database user, default is root [string]
	//opt: dbPass - database password, no default [string]
	//opt: dbName - database name, default is 'kapenta' [string]

	function KDBDriver_Dump() {
		global $registry;			// perhaps get these from $kapenta

		$this->name = '';

		if ('MySQL' == $registry->get('db.driver')) {
			$this->name = $registry->get('db.mysql.name');
		}

		if ('SQLite' == $registry->get('db.driver')) {
			$this->name = $registry->get('db.sqlite.name');
		}
	}

	//==============================================================================================
	//	WRAPPER METHODS - for MySQL
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	log a query to file
	//----------------------------------------------------------------------------------------------
	//arg: query - a SQL query [string]
	//returns: true on success or false on failure [bool]

	function query($query) {
		global $kapenta, $session, $page, $registry;

		$this->lasterr = '';						//	clear any previous error message
		$this->lastquery = $query;					//	record this query for debugging

		$this->count++;								//	increment query count for this page view
		$startTime = microtime(true);				//	record start time

		//------------------------------------------------------------------------------------------
		// open export file on first query
		//------------------------------------------------------------------------------------------

		if (false == $this->connected) {
			$this->dbh = fopen($this->name . '.sql', 'a+');

			if (false === $this->dbh) {
				$msg = "Could not open export file: " . $this->name;
				if (true == isset($session)) { $session->msgAdmin($msg, 'bad'); }
				else { echo $msg . "<br/>\n"; }
				return false;
			}

			$this->connected = true;

			$safeName = basename($this->name);
			$safeName = str_replace(' ', '_', $safeName);
			$safeName = str_replace(':', '_', $safeName);

			$initSql = ''
			 . "CREATE DATABASE IF NOT EXISTS " . $safeName
			 . " DEFAULT CHARACTER SET 'utf8';\n"
			 . "USE " . $safeName . ";\n\n";

			fwrite($this->dbh, $initSql);

		}

		//------------------------------------------------------------------------------------------
		// log the query
		//------------------------------------------------------------------------------------------
		$query = trim($query) . ";";
		$query = str_replace("\\", '\\', $query);		
		$query = str_replace("\r", '\r', $query);
		$query = str_replace("\n", '\n', $query);

		if (';;' == substr($query, -2)) { $query = substr($query, 0, strlen($query) - 1); }

		if (false !== strpos(' ' . $query, 'CREATE TABLE')) {
			$query = str_replace('\n', '', $query);
			$query = str_replace('; CREATE TABLE', ";\nCREATE TABLE", $query);
		}

		$written = fwrite($this->dbh, $query . "\n\n");

		if (false === $written) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	begin a transaction
	//----------------------------------------------------------------------------------------------
	//retuns: true (not implemented for this driver) [bool]

	function transactionStart() {
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	end a transaction
	//----------------------------------------------------------------------------------------------
	//retuns: true (not implemented for this driver) [bool]

	function transactionEnd() {
		return true;
	}	

	//----------------------------------------------------------------------------------------------
	//.	get a row from a recordset, not implemented in this dirver
	//----------------------------------------------------------------------------------------------
	//arg: handle - handle to a MySQL query result [int]
	//returns: associative array of field names and values, database markup not removed [array]

	function fetchAssoc($handle) { return array(); }

	//----------------------------------------------------------------------------------------------
	//.	get number of rows in recordset
	//----------------------------------------------------------------------------------------------
	//arg: handle - handle to a MySQL query result [int]
	//returns: number of rows or false on failure [int] [bool]

	function numRows($handle) { return false; }

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
	//.	load an object given it's type and UID, not implemented in this driver
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of record to load [string]
	//arg: dbSchema - database table definition [array]
	//returns: return associative array of field names and values or false on failure [array][bool]

	function load($UID, $dbSchema) { return false; }

	//----------------------------------------------------------------------------------------------
	//.	load a record from a table supporting recordAliases, return associative array
	//----------------------------------------------------------------------------------------------
	//arg: raUID - UID or alias of an object [string]
	//arg: dbSchema - database table definition [array]
	//returns: return associative array of field names and values or false on failure [array] [bool]

	function loadAlias($raUID, $dbSchema) { return false; }

	//----------------------------------------------------------------------------------------------
	//.	save a record given a dbSchema array and an associative array of fields and values
	//----------------------------------------------------------------------------------------------
	//arg: data - serialized object [array]
	//arg: dbSchema - schema of the database table this record belongs in [array]
	//opt: setdefaults - if true will automatically modify editedBy, editedOn, etc [bool]
	//opt: broadcast - if true, then pass to other peers [bool]
	//opt: revision - if true, then keep any revision data [bool]
	//returns: true on success, false on failure [bool]

	function save($data, $dbSchema, $setdefaults = false, $broadcast = false, $revision = false) {
		global $user, $revisions, $session, $kapenta;

		$changes = array();								//%	fields which have changed [dict]
		$dirty = false;									//%	if changes need to be saved [bool]
		$this->lasterr = '';

		if (false == is_array($data)) {
			$this->lasterr = 'No object array given.';
			return false;		// must include and object to save
		}

		if ((false == array_key_exists('UID', $data)) || (strlen(trim($data['UID'])) < 4)) {
			$this->lasterr = 'No valid UID for given object.';
			return false;		// object must have a UID, and it must be > 4 chars
		}

		$dbSchema['model'] = strtolower($dbSchema['model']);				// temporary

		//	adds support for primary keys for tables which must have both UID and Pi Key
		if (false == array_key_exists('prikey', $dbSchema)) { $dbSchema['prikey'] = ''; }

		//	do not save objects which have been deleted
		//	(not checked on export for performance reasons)
		/*
		if (true == $revisions->isDeleted($dbSchema['model'], $data['UID'])) {
			$this->lasterr = 'This object is deleted, must be undeleted before saving.';
			return false;
		}
		*/

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
		$current = $this->load($data['UID'], $dbSchema);
		
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

			$data = array();

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

		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	delete an object (if it exists), not implemented in this driver
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / name of database table [string]
	//arg: dbSchema - Database table schema [array]
	//returns: true on success, false on failure [bool]

	function delete($UID, $dbSchema) { return false; }

	//==============================================================================================
	//	OBJECT CACHE
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	store an object in the cache
	//----------------------------------------------------------------------------------------------
	//arg: model - type of object being stored [string]
	//arg: objAry - associative array of object members [string]

	function cacheStore($model, $objAry) { }

	//----------------------------------------------------------------------------------------------
	//.	update a record field without causing resync, strings only
	//----------------------------------------------------------------------------------------------
	//DEPRECATED: todo - find any calls to this and remove
	//arg: model - model name / name of database table [string]
	//arg: UID - UID of a record [string]
	//arg: field - field name [string]
	//arg: value - field value [string]
	//: this is used where an object should change only locally and changes not sent to peer servers

	function updateQuiet($model, $UID, $field, $value) { }

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

	function loadRange(
		$model,
		$fields = '*',
		$conditions = '',
		$by = '',
		$limit = '',
		$offset = ''
	) {
		return $array();
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

	function countRange($model, $conditions = '') { return 0; }

	//==============================================================================================
	//	TABLE METHODS - list, create, etc
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	make a list of all tables in database and store in $this->tables
	//----------------------------------------------------------------------------------------------
	//;	not implemented in this driver
	//returns: array of table names [array]

	function loadTables() { return array(); }

	//----------------------------------------------------------------------------------------------
	//.	alias if loadTables (preferred)
	//----------------------------------------------------------------------------------------------

	function listTables() { return $this->loadTables(); }

	//----------------------------------------------------------------------------------------------
	//.	check if a table exists in the database
	//----------------------------------------------------------------------------------------------
	//arg: model - model name / name of a database table [string]
	//returns: true if there exists a table with the name given, false if not found [bool]

	function tableExists($model) { return false; }

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

	function getSchema($tableName) { return $false; }

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

	function objectExists($model, $UID) { return false; }

	//==============================================================================================
	//	NON-SCHEMA IO - are in now wrappers 
	//==============================================================================================
	//TODO: consider whether the get and store methods are really necessary, remove if possible
	// but leave the serialization to and from XML

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database, serialized as an array
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / table name [string]
	//arg: UID - UID of an object in this table [string]
	//returns: object serialized as an array, empty array on failure [array]

	function getObject($model, $UID) { return array(); }

	//----------------------------------------------------------------------------------------------
	//.	load an object from the database, serialized as xml
	//----------------------------------------------------------------------------------------------
	//arg: model - object type / table name [string]
	//arg: UID - UID of an object in this table [string]
	//returns: object serialized as xml, empty string on failure [string]

	function getObjectXml($model, $UID) { return ''; }

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
		if (false == is_array($ary)) { return retVal; }
		foreach ($ary as $key => $val) { $retVal[$key] = $this->removeMarkup($val); }
		return $retVal;
	}
}

?>
