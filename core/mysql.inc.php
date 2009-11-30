<?

//--------------------------------------------------------------------------------------------------
//	core database functions for mysql
//--------------------------------------------------------------------------------------------------
// these are wrapper functions to allow the same function names (and thus code) on different DBMS
// note that % (sql wildcard) is stripped from UIDs from some functions as a security precaution

//--------------------------------------------------------------------------------------------------
// make a database (consider removing this, live sites should not need this functionality)
//--------------------------------------------------------------------------------------------------

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
// make a table + indices
//--------------------------------------------------------------------------------------------------

function dbCreateTable($dbSchema) {
  //if ($_SESSION['sGroup'] == 'admin') {

	//----------------------------------------------------------------------------------------------
	//	create table
	//----------------------------------------------------------------------------------------------

	$sql = "create table " . $dbSchema['table'] . " (\n";
	$fields = array();
	foreach($dbSchema['fields'] as $fieldName => $fieldType) {
		$fields[] = '  ' . $fieldName . ' ' . $fieldType;
	}
	$sql .= implode(",\n", $fields) . ");\n";

	//echo "<textarea rows='10' cols='60'>$sql</textarea>\n";
	dbQuery($sql);

	//----------------------------------------------------------------------------------------------
	//	indices
	//----------------------------------------------------------------------------------------------

	foreach($dbSchema['indices'] as $idxField => $idxSize) {
		$idxName = 'idx' . $dbSchema['table'] . $idxField;
		if ($idxSize == '') {
			$sql = "create index $idxName on " . $dbSchema['table'] . ";";
		} else {
			$sql = "create index $idxName on " . $dbSchema['table'] . " (" . $idxField . "(10));";
		}
		//echo "<textarea rows='10' cols='60'>$sql</textarea>\n";
		dbQuery($sql);
	}

  //}	
}

//--------------------------------------------------------------------------------------------------
// 	make a list of all tables in database
//--------------------------------------------------------------------------------------------------

function dbListTables() {
	global $dbName;			// database name (from setup.inc.php)
	$tables = array();
	$result = dbQuery("show tables from $dbName");
	while ($row = dbFetchAssoc($result)) { foreach ($row as $table) { $tables[] = $table; } }
	return $tables;
}

//--------------------------------------------------------------------------------------------------
// execute a query, return handle
//--------------------------------------------------------------------------------------------------

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
// load a record, return associative array
//--------------------------------------------------------------------------------------------------

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
// load a record from a table supporting recordAliases, return associative array
//--------------------------------------------------------------------------------------------------

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
// save a record given a dbSchema array and an array of field values, returns false on failue
//--------------------------------------------------------------------------------------------------

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
// update a record field without causing resync
//--------------------------------------------------------------------------------------------------

function dbUpdateQuiet($table, $UID, $field, $value) {	
	$sql = "update " . sqlMarkup($table) . " "
		 . "set " . sqlMarkup($field) . "='" . sqlMarkup($value) . "' "
		 . "where UID='" . sqlMarkup($UID) . "'";

	dbQuery($sql);
}

//--------------------------------------------------------------------------------------------------
// get a row from a recordset, number of rows in recordset
//--------------------------------------------------------------------------------------------------

function dbFetchAssoc($handle) { return mysql_fetch_assoc($handle); }
function dbNumRows($handle) { return mysql_num_rows($handle); }

//--------------------------------------------------------------------------------------------------
// delete a record
//--------------------------------------------------------------------------------------------------

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
// create an associative array of a recordset (expects UID for index)
//--------------------------------------------------------------------------------------------------

function dbQueryToArray($sql) {
	$result = dbQuery($sql);
	$recordSet = array();
	while ($row = mysql_fetch_assoc($result)) {
		$recordSet[$row['UID']] = $row;
	}
	return $recordSet;
}

//--------------------------------------------------------------------------------------------------
// 	check if a record with given UID exists in a table
//--------------------------------------------------------------------------------------------------

function dbRecordExists($dbTable, $UID) {
	$sql = "SELECT * FROM " . sqlMarkup($dbTable) . " WHERE UID='" . sqlMarkup($UID) . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) == 0) { return false; }
	return true;
}

//--------------------------------------------------------------------------------------------------
// 	check if a table exists in the database
//--------------------------------------------------------------------------------------------------

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
// 	create an associative array with fields unset
//--------------------------------------------------------------------------------------------------

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
// 	get a range of records
//--------------------------------------------------------------------------------------------------
//	
//	$conditions is an array of conditions, eg [ofgroup='admin'][table1.value=table2.value]
//	TODO: more security checks for sql inclusion, especially on conditons

function dbLoadRange($table, $fields, $conditions, $by, $limit, $offset) {

	//----------------------------------------------------------------------------------------------
	//	basic sql query to select all rows in table
	//----------------------------------------------------------------------------------------------
	if ($fields == '') { $fields = '*'; }
	$sql = "SELECT $fields FROM " . sqlMarkup($table) . " ";

	//----------------------------------------------------------------------------------------------
	//	conditions to winnow it down by
	//----------------------------------------------------------------------------------------------
	if ((is_array($conditions)) AND (count($conditions) > 0)) { 
		$where = implode(' AND ', $conditions); 
		$sql .= "WHERE $where "; 
	}

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
// 	get table schema in Kapenta's dbSchema format (jagged array)
//--------------------------------------------------------------------------------------------------
//	note that nodiff is not generated, as this is not known by the DBMS

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
// 	return a dbSchema array as html
//--------------------------------------------------------------------------------------------------

function dbSchemaToHtml($dbSchema) {
	$html = "<h2>" . $dbSchema['table'] . " (dbSchema)</h2>\n";
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
// 	sanitize a value before using it in sql statement, to prevent SQL injection and related attacks
//--------------------------------------------------------------------------------------------------

function sqlMarkup($text) {								// WHY?
	$text = str_replace('%', "[`|pc]", $text);			// wildcard characters in SQL
	$text = str_replace('_', "[`|us]", $text);			// ... 
	$text = str_replace(';', "[`|sc]", $text);			// used to construct SQL statements
	$text = str_replace("'", "[`|sq]", $text);			// ...
	$text = str_replace("\"", "[`|dq]", $text);			// ...
	$text = str_replace('<', "[`|lt]", $text);			// interference between nested XML schema
	$text = str_replace('>', "[`|gt]", $text);			// ...
	$text = str_replace("\t", "[`|tb]", $text);			// mysql errors
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
// 	remove sql markup
//--------------------------------------------------------------------------------------------------

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
// 	remove sql markup from an array (no nested arrays)
//--------------------------------------------------------------------------------------------------

function sqlRMArray($ary) {
	$retVal = array();
	if (is_array($ary) == true) {
		foreach ($ary as $key => $val) { $retVal[$key] = sqlRemoveMarkup($val);	}
	}
	return $retVal;
}

?>
