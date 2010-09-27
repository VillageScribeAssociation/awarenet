<?

//--------------------------------------------------------------------------------------------------
//*	administrative database functions for mysql
//--------------------------------------------------------------------------------------------------
//+	These are only used by administrative scripts (installation, maintenance, etc) and not loaded
//+	by ordinary actions on modules called by users.
//+
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

//--------------------------------------------------------------------------------------------------
//| make a database (consider removing this, live sites should not need this functionality)
//--------------------------------------------------------------------------------------------------
//arg: database - name of database [string]

function dbCreate($database) {
	global $user;
	if ('admin' == $user->role) {
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
	if ($user->role != 'admin') { return false; }			// only admins can do this
	if ($db->tableExists($dbSchema['table']) == true) { return false; }	// table already exists

	//----------------------------------------------------------------------------------------------
	//	create the table
	//----------------------------------------------------------------------------------------------
	$fields = array();
	foreach($dbSchema['fields'] as $fName => $fType) { $fields[] = '  ' . $fName . ' ' . $fType; }
	$sql = "create table " . $dbSchema['table'] . " (\n" . implode(",\n", $fields) . ");\n";
	$db->query($sql);

	if ($db->tableExists($dbSchema['table']) == false) { return false; }	// check that this worked

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
		$db->query($sql);
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
	$result = $db->query("show tables from $dbName");
	while ($row = $db->fetchAssoc($result)) { foreach ($row as $table) { $tables[] = $table; } }
	return $tables;
}

//--------------------------------------------------------------------------------------------------
//|	get table schema in Kapenta's dbSchema format (jagged array)
//--------------------------------------------------------------------------------------------------
//:note that nodiff is not generated, as this is not known by the DBMS
//arg: tableName - name of a database table [string]
//returns: nested array describing database table [array]

function dbGetSchema($tableName) {
	global $db;
	if ($db->tableExists($tableName) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	create dbSchema array
	//----------------------------------------------------------------------------------------------
	$dbSchema = array(	'table' => $tableName, 'fields' => array(), 
						'indices' => array(), 'nodiff' => array()	);

	//----------------------------------------------------------------------------------------------
	//	add fields
	//----------------------------------------------------------------------------------------------
	$sql = "describe " . $db->addMarkup($tableName);
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) 
		{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

	//----------------------------------------------------------------------------------------------
	//	add indices
	//----------------------------------------------------------------------------------------------
	$sql = "show indexes from " . $db->addMarkup($tableName);
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) 
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
	global $kapenta, $user;
	if ($user->role != 'admin') { return false; }	// only admins can do this

	$newName = $dbSchema['table'];
	$tmpName = 'tmp-' . $tableName . '-' . $kapenta->createUID();
	$newIndices = $dbSchema['indices'];
	if ($db->tableExists($tableName) == false) { return false; }

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
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) { $db->save($db->rmArray($row), $dbSchema); }

	//---------------------------------------------------------------------------------------------
	//	delete the original table and its indices
	//---------------------------------------------------------------------------------------------
	$sql = "show indexes from " . $db->addMarkup($tableName);
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) 
		{ $db->query("DROP INDEX " . $row['Key_name'] . " ON " . $tableName); }

	$db->query("DROP TABLE" . $tableName);

	//---------------------------------------------------------------------------------------------
	//	create new table with indices and copy all records from temporary table
	//---------------------------------------------------------------------------------------------
	$dbSchema['table'] = $newName;
	$dbSchema['indices'] = $newIndices;
	dbCreateTable($dbSchema);

	$sql = "select * from " . $newName . "copytemp";
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) { $db->save($db->rmArray($row), $dbSchema); }

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
	if ($db->tableExists('wiki') == false) { 
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
	if ($user->role != 'admin') { return false; }	// only admins can do this
	$report = '';

	if ($db->tableExists($dbSchema['table']) == false) {	
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
	global $db, $user;
	if ($user->role != 'admin') { return ''; }

	$installed = true;
	if ($db->tableExists($dbSchema['table']) == true) {
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
	
	if (true == $installed) { $report .= "<!-- table installed correctly -->"; }
	return $report;
}

?>
