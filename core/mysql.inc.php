<?

//--------------------------------------------------------------------------------------------------
//*	core database functions for mysql
//--------------------------------------------------------------------------------------------------
//+	note that these are deprecated wrappers to maintain some backwards compatability

//--------------------------------------------------------------------------------------------------
//|	execute a query, return handle
//--------------------------------------------------------------------------------------------------
//arg: query - a SQL query [string]
//returns: handle to query result [int]

function dbQuery($query) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbQuery() => $db->query()', 'bug');
	return $db->query($query);
}

//--------------------------------------------------------------------------------------------------
//|	load a record
//--------------------------------------------------------------------------------------------------
//arg: dbTable - database table name [string]
//arg: UID - UID of record to load
//returns: return associative array of field names and values or false on failure [array] [bool]

function dbLoad($dbTable, $UID) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbLoad() => $db->load()', 'bug');
	return $db->load($dbTable, $UID);
}

//--------------------------------------------------------------------------------------------------
//|	load a record from a table supporting recordAliases, return associative array
//--------------------------------------------------------------------------------------------------
//arg: dbTable - database table name [string]
//arg: UIDRA - UID or recordAlias of a record [string]
//returns: return associative array of field names and values or false on failure [array] [bool]

function dbLoadRa($dbTable, $raUID) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbLoadRa() => $db->loadAlias()', 'bug');
	return $db->loadAlias($dbTable, $raUID);
}

//--------------------------------------------------------------------------------------------------
//|	save a record given a dbSchema array and an array of field values, returns false on failue
//--------------------------------------------------------------------------------------------------
//arg: data - associative array of field names and values [array]
//arg: dbSchema - schema of the database table this record belongs in [array]
//returns: true on success, false on failure [bool]

function dbSave($data, $dbSchema) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbSave() => $db->save()', 'bug');
	return $db->save($data, $dbSchema);
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
	global $session, $db;
	$session->msgAdmin('deprecated: dbUpdateQuiet() => $db->updateQuiet()', 'bug');
	return $db->updateQuiet($query);
}

//--------------------------------------------------------------------------------------------------
//|	get a row from a recordset,
//--------------------------------------------------------------------------------------------------
//arg: handle - handle to a MySQL query result [int]
//returns: associative array of field names and values, database markup not removed [array]

function dbFetchAssoc($handle) { 
	global $session, $db;
	$session->msgAdmin('deprecated: dbFetchAssoc() => $db->fetchAssoc()', 'bug');
	return $db->fetchAssoc($handle);
}

//--------------------------------------------------------------------------------------------------
//|	get number of rows in recordset
//--------------------------------------------------------------------------------------------------
//arg: handle - handle to a MySQL query result [int]
//returns: number of rows or false on failure [int] [bool]

function dbNumRows($handle) { 	
	global $session, $db;
	$session->msgAdmin('deprecated: dbNumRows() => $db->numRows()', 'bug');
	return $db->numRows($handle);
}

//--------------------------------------------------------------------------------------------------
//|	delete a record
//--------------------------------------------------------------------------------------------------
//arg: dbTable - name of database table [string]
//arg: UID - UID of a record [string]
//returns: true on success, false on failure [bool]

function dbDelete($dbTable, $UID) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbDelete() => $db->delete()', 'bug');
	return $db->delete($dbTable, $UID);
}

//--------------------------------------------------------------------------------------------------
//|	create an associative array of a recordset (expects UID for index)
//--------------------------------------------------------------------------------------------------
//arg: sql - sql query [string]
//returns: array of associative arrays, database markup not removed [array]

function dbQueryToArray($sql) {
	global $session, $db;
	//TODO: find out of anything uses this, see if it can be removed
	$session->msgAdmin('deprecated: dbQueryToArray() => $db->queryToArray()', 'bug');
	return $db->queryToArray($sql);
}

//--------------------------------------------------------------------------------------------------
//|	check if a record with given UID exists in a table
//--------------------------------------------------------------------------------------------------
//arg: dbTable - name of database table [string]
//arg: UID - UID of a record [string]
//returns: true if record exists in the given table, false if not found [bool]

function dbRecordExists($dbTable, $UID) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbRecordExists() => $db->objectExists()', 'bug');
	return $db->objectExists($query);
}

//--------------------------------------------------------------------------------------------------
//|	check if a table exists in the database
//--------------------------------------------------------------------------------------------------
//arg: tableName - name of a database table [string]
//returns: true if there exists a table with the name given, false if not found [bool]

function dbTableExists($tableName) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbTableExists() => $db->tableExists()', 'bug');
	return $db->tableExists($tableName);
}

//--------------------------------------------------------------------------------------------------
//|	create an associative array with fields unset
//--------------------------------------------------------------------------------------------------
//arg: sqlData - a table schema, as with dbSchema [array]
//returns: copy of schema with magic fields filled in [array]

function dbBlank($dbSchema) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbBlank() => $db->makeBlankFromSchema()', 'bug');
	return $db->makeBlank($dbSchema);
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
//opt: by - field(s) to order by or empty string [string]
//opt: limit - max record to return [int]
//opt: offset - skip this many records from start [int]
//returns: array of associative arrays (field -> value) with database markup removed [array]

function dbLoadRange($table, $fields, $conditions, $by = '', $limit = '', $offset = '') {
	global $session, $db;
	$session->msgAdmin('deprecated: dbLoadRange() => $db->loadRange()', 'bug');
	return $db->loadRange($table, $fields, $conditions, $by, $limit, $offset);
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
	global $session, $db;
	$session->msgAdmin('deprecated: dbCountRange() => $db->countRange()', 'bug');
	return $db->countRange($table, $conditions);
}

//--------------------------------------------------------------------------------------------------
//|	sanitize a value before using it in sql statement, to prevent SQL injection and related attacks
//--------------------------------------------------------------------------------------------------
//arg: text - string to sanitize [string]
//returns: escaped string [string]

function dbMarkup($text) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbMarkup() => $db->addMarkup()', 'bug');
	return $db->addMarkup($sqlData);
}

//--------------------------------------------------------------------------------------------------
//|	compatability with older versions, deprecated
//--------------------------------------------------------------------------------------------------
//arg: text - string to sanitize [string]
//returns: escaped string [string]

function sqlMarkup($text) {
	global $session, $db;
	$session->msgAdmin('deprecated: sqlMarkup() => $db->addMarkup()', 'bug');
	return $db->addmarkup($text);
}

//--------------------------------------------------------------------------------------------------
//|	remove sql markup
//--------------------------------------------------------------------------------------------------
//arg: text - escaped string [string]
//returns: unescaped string [string]

function dbRmMarkup($text) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbRmMarkup() => $db->removeMarkup()', 'bug');
	return $db->removeMakrup($text);
}

//--------------------------------------------------------------------------------------------------
//|	compatability hack, deprecated
//--------------------------------------------------------------------------------------------------
//arg: text - escaped string [string]
//returns: unescaped string [string]

function sqlRemoveMarkup($text) {
 	global $session, $db;
	$session->msgAdmin('deprecated: sqlRemoveMarkup() => $db->removeMarkup()', 'bug');
	return $db->removeMarkup($text);
}

//--------------------------------------------------------------------------------------------------
//| remove sql markup from an array (no nested arrays)
//--------------------------------------------------------------------------------------------------
//arg: ary - associative array of field/value pairs [array]
//returns: same array with values unescaped (database markup removed) [array]

function dbRMArray($ary) {
	global $session, $db;
	$session->msgAdmin('deprecated: dbRMArray() => $db->rmArray()', 'bug');
	return $db->rmArray($ary);
}

//--------------------------------------------------------------------------------------------------
//| compatability with older versions, deprecated
//--------------------------------------------------------------------------------------------------
//arg: ary - associative array of field/value pairs [array]
//returns: same array with values unescaped (database markup removed) [array]

function sqlRMArray($ary) {
	global $session, $db;
	$session->msgAdmin('deprecated: sqlRMArray() => $db->rmArray()', 'bug');
	return $db->rmArray($ary);
}

?>
