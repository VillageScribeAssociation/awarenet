<?

//--------------------------------------------------------------------------------------------------
//*	core database functions for mysql
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

//+ TODO: consider splitting this into common and extended functions, and move extened functions
//+ to a separate file.  Functions only used for install scripts don't need to be loaded for
//+ every page render.

//--------------------------------------------------------------------------------------------------
//| make a database (consider removing this, live sites should not need this functionality)
//--------------------------------------------------------------------------------------------------
//arg: database - name of database [string]

function dbCreate($database) {
  if ($_SESSION['sGroup'] == 'admin') {
	$query = "create database $database";
	$connect = mysql_pconnect($dbHost, $dbUser, $dbPass) or die("no connect");
	$connect = mysql_connect($dbHost, $dbUser, $dbPass) or die("no connect");
	mysql_query($query, $connect) or die("<h1>Houston, we have a problem...</h1>" 
		. mysql_error() ."<p>" . $query);
  }
}

//--------------------------------------------------------------------------------------------------
//|	make a table + indices given dbSchema
//--------------------------------------------------------------------------------------------------
//arg: dbSchema - table schema [array]
//returns: true on success, false on failure [bool]

function dbCreateTable($dbSchema) {
	global $user;

	//----------------------------------------------------------------------------------------------
	//	checks
	//----------------------------------------------------------------------------------------------
	if ($user->data['ofGroup'] != 'admin') { return false; }			// only admins can do this
	if (dbTableExists($dbSchema['table']) == true) { return false; }	// table already exists

	//----------------------------------------------------------------------------------------------
	//	create the table
	//----------------------------------------------------------------------------------------------
	$fields = array();
	foreach($dbSchema['fields'] as $fName => $fType) { $fields[] = '  ' . $fName . ' ' . $fType; }
	$sql = "create table " . $dbSchema['table'] . " (\n" . implode(",\n", $fields) . ");\n";
	dbQuery($sql);

	if (dbTableExists($dbSchema['table']) == false) { return false; }	// check that this worked

	//----------------------------------------------------------------------------------------------
	//	create indices
	//----------------------------------------------------------------------------------------------
	foreach($dbSchema['indices'] as $idxField => $idxSize) {
		$idxName = 'idx' . $dbSchema['table'] . $idxField;
		if ($idxSize == '') {
			$sql = "create index $idxName on " . $dbSchema['table'] . ";";
		} else {
			$sql = "create index $idxName on " . $dbSchema['table'] . " (" . $idxField . "(10));";
		}
		dbQuery($sql);
		//TODO: check indices were created correctly
	}

	return true;
}

//--------------------------------------------------------------------------------------------------
//|	make a list of all tables in database
//--------------------------------------------------------------------------------------------------
//returns: array of table names [array]

function dbListTables() {
	global $dbName;			// database name (from setup.inc.php)
	$tables = array();
	$result = dbQuery("show tables from $dbName");
	while ($row = dbFetchAssoc($result)) { foreach ($row as $table) { $tables[] = $table; } }
	return $tables;
}

//--------------------------------------------------------------------------------------------------
//|	execute a query, return handle
//--------------------------------------------------------------------------------------------------
//arg: query - a SQL query [string]
//returns: handle to query result [int]

function dbQuery($query) {
	global $dbHost, $dbUser, $dbPass, $dbName;

	// connect to database
	$connect = mysql_pconnect($dbHost, $dbUser, $dbPass) or die("no connect");

	mysql_select_db($dbName, $connect); 

	$tempErr = error_reporting();
	error_reporting(E_ERROR);
	$result = mysql_query($query, $connect) or die("<h1>Houston, we have a problem...</h1>" 
		. mysql_error() ."<p>" . $query);

	error_reporting($tempErr);

	return $result;
}

//--------------------------------------------------------------------------------------------------
//|	load a record
//--------------------------------------------------------------------------------------------------
//arg: dbTable - database table name [string]
//arg: UID - UID of record to load
//returns: return associative array of field names and values or false on failure [array] [bool]

function dbLoad($dbTable, $UID) {
	$sql = "select * from $dbTable where UID='" . $UID . "'";
	$recordSet = dbQuery($sql);
	while ($record = mysql_fetch_assoc($recordSet))	 {
		$retVal = array();	// strip database markup 
		foreach($record as $fName => $fValue) {	$retVal[$fName] = sqlRemoveMarkup($fValue); }
		return $retVal;
	}
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	load a record from a table supporting recordAliases, return associative array
//--------------------------------------------------------------------------------------------------
//arg: dbTable - database table name [string]
//arg: UIDRA - UID or recordAlias of a record [string]
//returns: return associative array of field names and values or false on failure [array] [bool]

function dbLoadRa($dbTable, $UIDRA) {
	$sql = "select * from $dbTable where recordAlias='" . $UIDRA . "' or UID='" . $UIDRA . "'";
	$recordSet = dbQuery($sql);
	while ($record = mysql_fetch_assoc($recordSet))	 {
		$retVal = array();	// strip database markup 
		foreach($record as $fName => $fValue) {	$retVal[$fName] = sqlRemoveMarkup($fValue); }
		return $retVal;
	}
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	save a record given a dbSchema array and an array of field values, returns false on failue
//--------------------------------------------------------------------------------------------------
//arg: data - associative array of field names and values [array]
//arg: dbSchema - schema of the database table this record belongs in

function dbSave($data, $dbSchema) {
	global $user;
	if (array_key_exists('UID', $data) == false) { return false; }	
	if (strlen(trim($data['UID'])) < 4) { return false; }

	//----------------------------------------------------------------------------------------------
	//	set editedBy, editedOn if present is schema
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('editedBy', $dbSchema['fields']) == true) 
		{ $data['editedBy'] = $user->data['UID']; }

	if (array_key_exists('editedOn', $dbSchema['fields']) == true) 
		{ $data['editedOn'] = mysql_datetime(); }

	//----------------------------------------------------------------------------------------------
	//	if previous version of record exists, save changes to changes record
	//----------------------------------------------------------------------------------------------

	$current = dbLoad($dbSchema['table'], $data['UID']);
	$changes = '';

	if ($current != false) {
	  foreach($current as $fName => $fVal) {
	    if ((array_key_exists($fName, $dbSchema['nodiff']) == false) 
		&& (array_key_exists($fName, $data) == true)) {

		//------------------------------------------------------------------------------------------
		//	if this field has changed, and is not a nodiff, save the old value to changes
		//------------------------------------------------------------------------------------------
		if ($fVal != $data[$fName]) { $changes .= "<$fName>" . sqlMarkup($fVal) . "</$fName>\n"; }

	    }
	  }
	}	

	if (strlen($changes) > 0) {
		//$sql = "insert into changes ";
	}

	//----------------------------------------------------------------------------------------------
	//	delete the current record
	//----------------------------------------------------------------------------------------------

	$sql = "delete from " . $dbSchema['table'] . " where UID='" . $data['UID'] . "'";
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	save a new one
	//----------------------------------------------------------------------------------------------

	$sql = "insert into " . $dbSchema['table'] . " values (";
	foreach ($dbSchema['fields'] as $fName => $fType) {
	  if (strlen($fName) > 0) {
		$quote = true;
		$value = ''; // . $fName . ':';

		//------------------------------------------------------------------------------------------
		//	some field types should be quotes, some not
		//------------------------------------------------------------------------------------------
		switch (strtolower($fType)) {
			case 'bigint': 		$quote = false; break;
			case 'tinyint';		$quote = false; break;
		}

		//------------------------------------------------------------------------------------------
		//	clean the value and add to array
		//------------------------------------------------------------------------------------------
		if (array_key_exists($fName, $data)) { $value = sqlMarkup($data[$fName]); } 
		if ($quote) { $value = "\"" . $value . "\""; }
		$sql .= $value . ',';
	   }
	}

	$sql = substr($sql, 0, strlen($sql) - 1);
	$sql .= ");";	
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	pass on to peers
	//----------------------------------------------------------------------------------------------

	syncBroadcastDbUpdate('self', $dbSchema['table'], $data);

}

//--------------------------------------------------------------------------------------------------
//|	update a record field without causing resync, strings only
//--------------------------------------------------------------------------------------------------
//arg: table - name of database table [string]
//arg: UID - UID of a record [string]
//arg: field - field name [string]
//arg: value - field value [string]
//: this is used where a record should change only locally and changes not sent to peer servers

function dbUpdateQuiet($table, $UID, $field, $value) {	
	$sql = "update " . sqlMarkup($table) . " "
		 . "set " . sqlMarkup($field) . "='" . sqlMarkup($value) . "' "
		 . "where UID='" . sqlMarkup($UID) . "'";

	dbQuery($sql);
}

//--------------------------------------------------------------------------------------------------
//|	get a row from a recordset,
//--------------------------------------------------------------------------------------------------
//arg: handle - handle to a MySQL query result [int]
//returns: associative array of field names and values, database markup not removed [array]

function dbFetchAssoc($handle) { return mysql_fetch_assoc($handle); }

//--------------------------------------------------------------------------------------------------
//|	get number of rows in recordset
//--------------------------------------------------------------------------------------------------
//arg: handle - handle to a MySQL query result [int]
//returns: number of rows or false on failure [int] [bool]

function dbNumRows($handle) { return mysql_num_rows($handle); }

//--------------------------------------------------------------------------------------------------
//|	delete a record
//--------------------------------------------------------------------------------------------------
//arg: dbTable - name of database table [string]
//arg: UID - UID of a record [string]
//returns: true on success, false on failure [bool]

function dbDelete($dbTable, $UID) {
	//---------------------------------------------------------------------------------------------
	//	delete the record if it exists
	//---------------------------------------------------------------------------------------------
	if (dbRecordExists($dbTable, $UID) == true) {
		$sql = "delete from " . sqlMarkup($dbTable) . " where UID='" . sqlMarkup($UID) . "'";
		dbQuery($sql);
		syncRecordDeletion($dbTable, $UID);

		//-----------------------------------------------------------------------------------------
		//	delete any recordAliases this item might have
		//-----------------------------------------------------------------------------------------
		raDeleteAll($dbTable, $UID);

		//-----------------------------------------------------------------------------------------
		//	send event to any modules which may need to do something about this
		//-----------------------------------------------------------------------------------------
		//$args = array('table' => $dbTable, 'UID' => $UID);
		//eventSendAll('record_deleted', $args);				// nothing uses this yet
		return true;

	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	create an associative array of a recordset (expects UID for index)
//--------------------------------------------------------------------------------------------------
//arg: sql - sql query [string]
//returns: array of associative arrays, database markup not removed [array]

function dbQueryToArray($sql) {
	$result = dbQuery($sql);
	$recordSet = array();
	while ($row = mysql_fetch_assoc($result)) {
		$recordSet[$row['UID']] = $row;
	}
	return $recordSet;
}

//--------------------------------------------------------------------------------------------------
//|	check if a record with given UID exists in a table
//--------------------------------------------------------------------------------------------------
//arg: dbTable - name of database table [string]
//arg: UID - UID of a record [string]
//returns: true if record exists in the given table, false if not found [bool]

function dbRecordExists($dbTable, $UID) {
	$sql = "SELECT * FROM " . sqlMarkup($dbTable) . " WHERE UID='" . sqlMarkup($UID) . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) == 0) { return false; }
	return true;
}

//--------------------------------------------------------------------------------------------------
//|	check if a table exists in the database
//--------------------------------------------------------------------------------------------------
//arg: tableName - name of a database table [string]
//returns: true if there exists a table with the name given, false if not found [bool]

function dbTableExists($tableName) {
	global $dbName;
	$sql = "SHOW TABLES FROM $dbName";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) {
	  foreach ($row as $key => $someTable) {
		if ($someTable == $tableName) { return true; }
	  }
	}
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	create an associative array with fields unset
//--------------------------------------------------------------------------------------------------
//arg: sqlData - a table schema, as with dbSchema [array]
//returns: copy of schema with magic fields filled in [array]

function dbBlank($sqlData) {
	$blank = array();
	foreach($sqlData['fields'] as $fieldName => $fieldType) {
		$fieldType = strtoupper($fieldType);
		$blank[$fieldName] = '';

		if ($fieldType == 'DATETIME') {	$blank[$fieldName] = mysql_datetime(); }
		if ($fieldType == 'TINYINT') { $blank[$fieldName] = '0'; }

		if ($fieldName == 'UID') { $blank[$fieldName] = createUID(); }
		if ($fieldName == 'createdBy') { $blank[$fieldName] = $_SESSION['sUserUID']; }
		if ($fieldName == 'editedBy') { $blank[$fieldName] = $_SESSION['sUserUID']; }
		if ($fieldName == 'editedOn') { $blank[$fieldName] = mysql_datetime(); }
		if ($fieldName == 'createdOn') { $blank[$fieldName] = mysql_datetime(); }
	}

	return $blank;
}

//--------------------------------------------------------------------------------------------------
//|	get a range of records
//--------------------------------------------------------------------------------------------------
//: if fields is empty string then all fields (*) are assumed
//:	'conditions' is an array of conditions, eg 1 => ofGroup='admin', 2 => table1.value=table2.value
//:	TODO: more security checks for sql inclusion, especially on conditons

//arg: table - name of database table [string]
//arg: fields - list of fields or empty string (SELECT clause) [string]
//arg: conditions - array of conditions (WHERE clause) [array]
//arg: by - field(s) to order by or empty string [string]
//arg: limit - max record to return [int]
//arg: offset - skip this many records from start [int]
//returns: array of associative arrays (field -> value) with database markup removed [array]

function dbLoadRange($table, $fields, $conditions, $by, $limit, $offset) {

	//----------------------------------------------------------------------------------------------
	//	basic sql query to select all rows in table
	//----------------------------------------------------------------------------------------------
	if ($fields == '') { $fields = '*'; }
	$sql = "SELECT $fields FROM " . sqlMarkup($table) . " ";

	//----------------------------------------------------------------------------------------------
	//	conditions to winnow it down by
	//----------------------------------------------------------------------------------------------
	if ((is_array($conditions)) AND (count($conditions) > 0)) 
		{ $sql .= 'WHERE ' . implode(' AND ', $conditions) . ' '; }

	//----------------------------------------------------------------------------------------------
	//	order by a particular field
	//----------------------------------------------------------------------------------------------
	if (strlen($by) > 0) { $sql .= "ORDER BY $by "; }

	//----------------------------------------------------------------------------------------------
	//	max n results
	//----------------------------------------------------------------------------------------------
	if (($limit != false) AND ($limit != '')) 
		{ $limit = sqlMarkup($limit); $sql .= "LIMIT $limit "; }

	//----------------------------------------------------------------------------------------------
	//	starting from
	//----------------------------------------------------------------------------------------------
	if (($offset != false) AND ($offset != '')) 
		{ $offset = sqlMarkup($offset); $sql .= "OFFSET $offset "; }

	//----------------------------------------------------------------------------------------------
	//	execute the query and return results as associative array
	//----------------------------------------------------------------------------------------------
	$result = dbQuery($sql);
	$retVal = array();

	while ($row = dbFetchAssoc($result)) {
		foreach ($row as $fName => $fValue) { $row[$fName] = sqlRemoveMarkup($fValue); }
		$retVal[$row['UID']] = $row;
	}	

	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//|	count a subset of records
//--------------------------------------------------------------------------------------------------
//: if fields is empty string then all fields (*) are assumed
//:	'conditions' is an array of conditions, eg 1 => ofGroup='admin', 2 => table1.value=table2.value
//:	TODO: more security checks for sql inclusion, especially on conditons

//arg: table - name of database table [string]
//arg: conditions - array of conditions (WHERE clause) [array]
//returns: number of records [int]

function dbCountRange($table, $conditions) {
	//----------------------------------------------------------------------------------------------
	//	basic sql query to count UIDs
	//----------------------------------------------------------------------------------------------
	if ($fields == '') { $fields = '*'; }
	$sql = "SELECT count(UID) as numRows FROM " . sqlMarkup($table) . " ";

	//----------------------------------------------------------------------------------------------
	//	conditions to winnow it down by
	//----------------------------------------------------------------------------------------------
	if ((is_array($conditions)) AND (count($conditions) > 0)) 
		{ $sql .= 'WHERE ' . implode(' AND ', $conditions) . ' '; }

	//----------------------------------------------------------------------------------------------
	//	execute the query and return results as associative array
	//----------------------------------------------------------------------------------------------
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) { return $row['numRows']; }
	return false;
}

//--------------------------------------------------------------------------------------------------
//|	get table schema in Kapenta's dbSchema format (jagged array)
//--------------------------------------------------------------------------------------------------
//:note that nodiff is not generated, as this is not known by the DBMS
//arg: tableName - name of a database table [string]
//returns: nested array describing database table [array]

function dbGetSchema($tableName) {
	if (dbTableExists($tableName) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	create dbSchema array
	//----------------------------------------------------------------------------------------------
	$dbSchema = array(	'table' => $tableName, 'fields' => array(), 
						'indices' => array(), 'nodiff' => array()	);

	//----------------------------------------------------------------------------------------------
	//	add fields
	//----------------------------------------------------------------------------------------------
	$sql = "describe " . sqlMarkup($tableName);
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) 
		{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

	//----------------------------------------------------------------------------------------------
	//	add indices
	//----------------------------------------------------------------------------------------------
	$sql = "show indexes from " . sqlMarkup($tableName);
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) 
		{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

	return $dbSchema;
}

//--------------------------------------------------------------------------------------------------
//|	return a dbSchema array as html
//--------------------------------------------------------------------------------------------------
//: this is mostly for debugging and administrative display
//arg: dbSchema - a database table schema [array]
//returns: HTML table with kapenta 'scaffold' style [string]

function dbSchemaToHtml($dbSchema, $title = '') {
	if ('' == $title) { $dbSchema['table'] . " (dbSchema)"; }		// default title
	$html = "<h2>" . $title . "</h2>\n";
	$rows = array(array('Field', 'Type', 'Index'));
	foreach($dbSchema['fields'] as $field => $type) {
		$idx = '';
		if (array_key_exists($field, $dbSchema['indices'])) { $idx = $dbSchema['indices'][$field]; }
		$rows[] = array($field, $type, $idx);
	}

	$html .= arrayToHtmlTable($rows, true, true);
	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	compare two database table schema
//--------------------------------------------------------------------------------------------------
//: this is mostly for debugging and administrative display, and used by updates
//arg: dbSchema1 - a database table schema [array]
//arg: dbSchema2 - a database table schema [array]
//returns: true if they check out, false if they are different
//: nodiff fields are not included, since the database doesn't look after these
//: note that tables may have different names

function dbCompareSchema($dbSchema1, $dbSchema2) {
	if ($dbSchema1['table'] != $dbSchema2['table']) { return false; }
	$tStr1 = ''; $tStr2 = '';

	//----------------------------------------------------------------------------------------------
	//	fields must be in same order
	//----------------------------------------------------------------------------------------------
	foreach($dbSchema1['fields'] as $key => $val) { $tStr1 .= $key . '|' . $val . "\n"; }
	foreach($dbSchema2['fields'] as $key => $val) { $tStr2 .= $key . '|' . $val . "\n"; }
	if ($tStr1 != $tStr2) { 
		return false; 
	}

	//----------------------------------------------------------------------------------------------
	//	indices need not be in same order
	//----------------------------------------------------------------------------------------------
	foreach($dbSchema1['indices'] as $field => $size) {
		if (array_key_exists($field, $dbSchema2['indices']) == false) { return false; }
		if ($dbSchema2['indices'][$field] != $size) { return false; }
	}

	foreach($dbSchema2['indices'] as $field => $size) {
		if (array_key_exists($field, $dbSchema1['indices']) == false) { return false; }
		if ($dbSchema1['indices'][$field] != $size) { return false; }
	}

	return true;	// nothing turned out to be incorrect
}

//--------------------------------------------------------------------------------------------------
//|	recreate a table to a given schema
//--------------------------------------------------------------------------------------------------
//: this is mostly for debugging and administrative display
//arg: tableName - name of a database table [string]
//arg: dbSchema - a database table schema [array]
//returns: true on success, false if it fails [bool]

function dbRecreateTable($tableName, $dbSchema) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this

	$newName = $dbSchema['table'];
	$tmpName = 'tmp-' . $tableName . '-' . createUID();
	$newIndices = $dbSchema['indices'];
	if (dbTableExists($tableName) == false) { return false; }

	//---------------------------------------------------------------------------------------------
	//	create a new, temporary table according to schema without indices and revisioning
	//---------------------------------------------------------------------------------------------
	$dbSchema['table'] .= $newName;
	$dbSchema['indices'] = array();
	$dbSchema['nodiff'] = array();
	foreach($dbSchema['fields'] as $fName => $fType) { $dbSchema['nodiff'][] = $fName; }
	$check = dbCreateTable($dbSchema);
	if (false == $check) { return false; }

	//---------------------------------------------------------------------------------------------
	//	copy all records into temp table
	//---------------------------------------------------------------------------------------------
	$sql = "select * from $tableName";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) { dbSave(sqlRMArray($row), $dbSchema); }

	//---------------------------------------------------------------------------------------------
	//	delete the original table and its indices
	//---------------------------------------------------------------------------------------------
	$sql = "show indexes from " . sqlMarkup($tableName);
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) 
		{ dbQuery("DROP INDEX " . $row['Key_name'] . " ON " . $tableName); }

	dbQuery("DROP TABLE" . $tableName);

	//---------------------------------------------------------------------------------------------
	//	create new table with indices and copy all records from temporary table
	//---------------------------------------------------------------------------------------------
	$dbSchema['table'] = $newName;
	$dbSchema['indices'] = $newIndices;
	dbCreateTable($dbSchema);

	$sql = "select * from " . $newName . "copytemp";
	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) { dbSave(sqlRMArray($row), $dbSchema); }

	return true;
}

//--------------------------------------------------------------------------------------------------
//|	sanitize a value before using it in sql statement, to prevent SQL injection and related attacks
//--------------------------------------------------------------------------------------------------
//arg: tableName - name of a database table [string]
//returns: html report of table status [string]
//: TODO: remove this, redundant test code

function dbCheckSchema($tableName, $dbSchema) {
	$report = '';
	if (dbTableExists('wiki') == false) { 
		$report .= "[*] Database table '$tableName' is not installed.<br/>\n";
	} else {
		$report .= "[*] Database table '$tableName' exists.<br/>\n";
		$model = new Wiki();
		$modelSchema = $model->initDbSchema();
		$liveSchema = dbGetSchema('wiki');
		if (dbCompareSchema($modelSchema , $liveSchema) == false) {
			$report .= "[*] Table '$tableName' exists but does not match schema.<br/>\n";
		} else {
			$report .= "[*] Table '$tableName' and indices match schema.<br/>\n";
		}
	}
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	create or recreate a table given its schema
//--------------------------------------------------------------------------------------------------
//arg: dbSchema - a database table specification [array]
//returns: html report of actions taken, false on failure [string]

function dbInstallTable($dbSchema) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }	// only admins can do this
	$report = '';

	if (dbTableExists($dbSchema['table']) == false) {	
		//-----------------------------------------------------------------------------------------
		//	no such table, create it
		//-----------------------------------------------------------------------------------------
		$report .= "Creating " . $dbSchema['table'] . " table and indices... ";
		$check = dbCreateTable($dbSchema);	
		if (true == $check) { $report .= "<span class='ajaxmsg'>done</span><br/>\n"; }
		else { $report .= "<span class='ajaxerror'>failed</span><br/>\n"; }

	} else {
		//-----------------------------------------------------------------------------------------
		//	table exists, check if its up to date
		//-----------------------------------------------------------------------------------------
		$report .= "Gallery table already exists...";	
		$extantSchema = dbGetSchema($dbSchema['table']);		// load specifics of extant table

		if (dbCompareSchema($dbSchema, $extantSchema) == true) {
			$report .= "<span class='ajaxmsg'>all correct</span><br/>";
		} else {
			$check = dbRecreateTable($dbSchema);
			if (true == $check) { 
				$report .= "<span class='ajaxmsg'>updated to new schema</span><br/>";
			} else {
				$report .= "<span class='ajaxerr'>could not update</span><br/>";
			}
		}
	}


	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	create a report describing a table's install status
//--------------------------------------------------------------------------------------------------
//arg: dbSchema - a database table specification [array]
//returns: html report of actions taken [string]

function dbGetTableInstallStatus($dbSchema) {
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return ''; }

	$installed = true;
	if (dbTableExists($dbSchema['table']) == true) {
		//-----------------------------------------------------------------------------------------
		//	table present
		//-----------------------------------------------------------------------------------------
		$extantSchema = dbGetSchema($dbSchema['table']);

		if (dbCompareSchema($dbSchema, $extantSchema) == false) {
			//-------------------------------------------------------------------------------------
			// table schemas DO NOT match (fail)
			//-------------------------------------------------------------------------------------
			$installed = false;		
			$report .= "<p>A '" . $dbSchema['table'] . "' table exists, but does not match "
					 . "object's schema.</p>\n"
					 . "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n"
					 . "<b>Extant Table:</b><br/>\n" . dbSchemaToHtml($extantSchema) . "<br/>\n";

		} else {
			//-------------------------------------------------------------------------------------
			// table schemas match
			//-------------------------------------------------------------------------------------
			$report .= "<p>'" . $dbSchema['table'] . "' table exists, matches object schema.</p>\n"
					 . "<b>Database Table:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";

		}

	} else {
		//-----------------------------------------------------------------------------------------
		//	table missing (fail)
		//-----------------------------------------------------------------------------------------
		$installed = false;
		$report .= "<p>'" . $dbSchema['table'] . "' table does not exist in the database.</p>\n"
				 . "<b>Object Schema:</b><br/>\n" . dbSchemaToHtml($dbSchema) . "<br/>\n";
	}
	
	if (true == $installed) { $report .= "<!-- installed correctly -->"; }
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	sanitize a value before using it in sql statement, to prevent SQL injection and related attacks
//--------------------------------------------------------------------------------------------------
//arg: text - string to sanitize [string]
//returns: escaped string [string]

function sqlMarkup($text) {								// WHY?
	$text = str_replace('%', "[`|pc]", $text);			// wildcard characters in SQL
	$text = str_replace('_', "[`|us]", $text);			// ... 
	$text = str_replace(';', "[`|sc]", $text);			// used to construct SQL statements
	$text = str_replace("'", "[`|sq]", $text);			// ...
	$text = str_replace("\"", "[`|dq]", $text);			// ...
	$text = str_replace('<', "[`|lt]", $text);			// interference between nested XML schema
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

//--------------------------------------------------------------------------------------------------
//|	remove sql markup
//--------------------------------------------------------------------------------------------------
//arg: text - escaped string [string]
//returns: unescaped string [string]

function sqlRemoveMarkup($text) {
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

	//----------------------------------------------------------------------------------------------
	// legacy markup, from kapenta 1, remove these if not migrating old data
	//----------------------------------------------------------------------------------------------

	$text = str_replace("[`|squote]", "'", $text);
	$text = str_replace("[`|quote]", "\"", $text);
	$text = str_replace("[`|semicolon]", ";", $text);

	return $text;
}

//--------------------------------------------------------------------------------------------------
//| remove sql markup from an array (no nested arrays)
//--------------------------------------------------------------------------------------------------
//arg: ary - associative array of field/value pairs [array]
//returns: same array with values unescaped (database markup removed) [array]

function sqlRMArray($ary) {
	$retVal = array();
	if (is_array($ary) == true) {
		foreach ($ary as $key => $val) { $retVal[$key] = sqlRemoveMarkup($val);	}
	}
	return $retVal;
}

?>
