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


class KDBAdminDriver {

	//----------------------------------------------------------------------------------------------
	//.	make a database (consider removing this, live sites should not need this functionality)
	//----------------------------------------------------------------------------------------------
	//arg: database - name of database [string]

	function create($database) {
		global $db, $user;

		if ('admin' == $user->role) {
		$query = 'CREATE DATABASE ' . $database;
		$connect = mysql_pconnect($db->host, $db->user, $db->pass) or die("no connect");
		//$connect = mysql_connect($db->host, $db->user, $db->pass) or die("no connect");
		mysql_query($query, $connect) or die("<h1>Houston, we have a problem...</h1>" 
			. mysql_error() ."<p>" . $query);
	  }
	}

	//----------------------------------------------------------------------------------------------
	//.	make a table + indices given dbSchema
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - table schema [array]
	//returns: true on success, false on failure [bool]

	function createTable($dbSchema) {
		global $user, $db;
		//------------------------------------------------------------------------------------------
		//	checks
		//------------------------------------------------------------------------------------------
		//if ('admin' != $user->role) { return false; }						// admins only
		if ($db->tableExists($dbSchema['table']) == true) { return false; }	// table already exists

		//------------------------------------------------------------------------------------------
		//	create the table
		//------------------------------------------------------------------------------------------
		$fields = array();
		foreach($dbSchema['fields'] as $fName => $fType) { $fields[] = '  '. $fName .' '. $fType; }
		$sql = "CREATE TABLE " . $dbSchema['table'] . " (\n" . implode(",\n", $fields) . ");\n";
		$db->query($sql);

		if (false == $db->tableExists($dbSchema['table'])) { return false; }	// check it worked

		//------------------------------------------------------------------------------------------
		//	create indices
		//----------------------------------------------------------------------------------------------
		foreach($dbSchema['indices'] as $idxField => $idxSize) {
			$idxName = 'idx' . $dbSchema['table'] . $idxField;
			if ($idxSize == '') {
				$sql = "CREATE INDEX $idxName ON " . $dbSchema['table'] . " (" . $idxField . ");";
			} else {
				$sql = "CREATE INDEX $idxName ON " . $dbSchema['table'] . " (" . $idxField . "(10));";
			}
			$db->query($sql);
			//TODO: check indices were created correctly
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	copy all records from one table to another
	//----------------------------------------------------------------------------------------------
	//arg: fromTable - name of database table to copy from [string]
	//arg: toSchema - name of database table to copy to [array]
	//arg: rename - map of fields to rename (old => new) [array]
	//returns: number of records copied [int]

	function copyAll($fromTable, $toSchema, $rename) {
		global $db;
		$count = 0;			//%	return value, number of records copied [int]

		if (false == $db->tableExists($fromTable)) { return false; }
		if (false == $db->tableExists($toSchema['table'])) { return false; }		

		$range = $db->loadRange($fromTable, '*', '');
		foreach($range as $row) {
			if (false == $db->objectExists($toSchema['table'], $row['UID'])) {
				$newObj = array();

				// default values
				foreach($toSchema['fields'] as $fName => $fType) {
					switch(strtolower($fType)) {
						case 'bigint':		$newObj[$fName] = '0';	break;
						case 'int':			$newObj[$fName] = '0';	break;
						case 'float':		$newObj[$fName] = '0';	break;
						case 'datetime':	$newObj[$fName] = $db->datetime();	break;
						default:			$newObj[$fName] = '';	break;
					}
					if (true == array_key_exists($fName, $row)) { $newObj[$fName] = $row[$fName]; }
				}

				// rename fields
				foreach($rename as $fromName => $toName) { $newObj[$toName] = $row[$fromName]; }

				$db->save($newObj, $toSchema);	
				$count++;
			}
		}
		return $count;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a list of all tables in database
	//----------------------------------------------------------------------------------------------
	//returns: array of table names [array]

	function listTables() {
		global $db;

		$tables = array();
		$result = $db->query("SHOW TABLES FROM " . $db->name);
		while ($row = $db->fetchAssoc($result)) { foreach ($row as $table) { $tables[] = $table; } }
		return $tables;
	}

	//----------------------------------------------------------------------------------------------
	//.	get table schema in Kapenta's dbSchema format (jagged array)
	//----------------------------------------------------------------------------------------------
	//:note that nodiff is not generated, as this is not known by the DBMS
	//arg: tableName - name of a database table / model name [string]
	//returns: nested array describing database table [array]

	function getSchema($tableName) {
		global $db;
		if (false == $db->tableExists($tableName)) { return false; }

		//------------------------------------------------------------------------------------------
		//	create dbSchema array
		//------------------------------------------------------------------------------------------
		$dbSchema = array(
			'table' => $tableName,
			'fields' => array(),
			'indices' => array(),
			'nodiff' => array()
		);

		//------------------------------------------------------------------------------------------
		//	add fields
		//------------------------------------------------------------------------------------------
		$sql = "describe " . $tableName;
		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) 
			{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

		//------------------------------------------------------------------------------------------
		//	add indices
		//------------------------------------------------------------------------------------------
		$sql = "show indexes from " . $tableName;
		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) 
			{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

		return $dbSchema;
	}

	//----------------------------------------------------------------------------------------------
	//.	return a dbSchema array as html
	//----------------------------------------------------------------------------------------------
	//: this is mostly for debugging and administrative display
	//arg: dbSchema - a database table schema [array]
	//returns: HTML table with kapenta 'scaffold' style [string]

	function schemaToHtml($dbSchema, $title = '') {
		global $theme;

		if ('' == $title) { $title = $dbSchema['table'] . " (dbSchema)"; }		// default title
		$html = "<h2>" . $title . "</h2>\n";
		$rows = array(array('Field', 'Type', 'Index'));
		foreach($dbSchema['fields'] as $field => $type) {
			$idx = '';
			if (array_key_exists($field, $dbSchema['indices'])) { 
				if ('' == $dbSchema['indices'][$field]) { $idx = 'YES'; }
				else { $idx = 'YES (' . $dbSchema['indices'][$field] . ')'; }
			}
			$rows[] = array($field, $type, $idx);
		}

		$html .= $theme->arrayToHtmlTable($rows, true, true);
		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//.	compare two database table schema
	//----------------------------------------------------------------------------------------------
	//: this is mostly for debugging and administrative display, and used by updates
	//arg: dbSchema1 - a database table schema [array]
	//arg: dbSchema2 - a database table schema [array]
	//returns: true if they check out, false if they are different
	//: nodiff fields are not included, since the database doesn't look after these
	//: note that tables may have different names

	function compareSchema($dbSchema1, $dbSchema2) {
		if ($dbSchema1['table'] != $dbSchema2['table']) { return false; }
		$tStr1 = ''; $tStr2 = '';

		//------------------------------------------------------------------------------------------
		//	fields must be in same order
		//------------------------------------------------------------------------------------------
		foreach($dbSchema1['fields'] as $key => $val) { $tStr1 .= $key . '|' . $val . "\n"; }
		foreach($dbSchema2['fields'] as $key => $val) { $tStr2 .= $key . '|' . $val . "\n"; }
		if ($tStr1 != $tStr2) { 
			return false; 
		}

		//------------------------------------------------------------------------------------------
		//	indices need not be in same order
		//------------------------------------------------------------------------------------------
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

	//----------------------------------------------------------------------------------------------
	//.	recreate a table to a given schema
	//----------------------------------------------------------------------------------------------
	//: this is mostly for debugging and administrative display
	//arg: tableName - name of a database table / model name [string]
	//arg: dbSchema - a database table schema [array]
	//returns: true on success, false if it fails [bool]

	function recreateTable($tableName, $dbSchema) {
		global $kapenta, $db, $user;
		if ($user->role != 'admin') { return false; }	// only admins can do this

		//------------------------------------------------------------------------------------------
		//	create a new, temporary table according to schema without indices and revisioning
		//------------------------------------------------------------------------------------------

		$tmpSchema = array(
			'module' => '',
			'table' => 'tmp_' . $tableName . '_' . $kapenta->createUID(),
			'fields' => array(),
			'indices' => array()
		);

		foreach($dbSchema['fields'] as $fName => $fType) { $tmpSchema['fields'][$fName] = $fType; }
		foreach($dbSchema['fields'] as $fName => $size) { $tmpSchema['nodiff'][] = $fName; }

		$check = $this->createTable($dbSchema);
		if (false == $check) { return false; }

		//------------------------------------------------------------------------------------------
		//	copy all records into temp table
		//------------------------------------------------------------------------------------------
		$sql = 'SELECT * FROM ' . $tableName;
		$result = $db->query($sql);
		//TODO: more intelligence here
		while ($row = $db->fetchAssoc($result)) { $db->save($db->rmArray($row), $tmpSchema); }

		//------------------------------------------------------------------------------------------
		//	delete the original table and its indices
		//------------------------------------------------------------------------------------------
		$sql = "show indexes from " . $tableName;
		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) 
			{ $db->query("DROP INDEX " . $row['Key_name'] . " ON " . $tableName); }

		$db->query('DROP TABLE ' . $tableName);

		//------------------------------------------------------------------------------------------
		//	create new table with indices and copy all records from temporary table
		//------------------------------------------------------------------------------------------
		$this->createTable($dbSchema);
		$sql = "select * from " . $tmpSchema['table'];
		$result = $db->query($sql);
		while ($row = $db->fetchAssoc($result)) { $db->save($db->rmArray($row), $dbSchema); }

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	check that a database schema matches the existing table
	//----------------------------------------------------------------------------------------------
	//arg: tableName - name of a database table [string]
	//returns: html report of table status [string]
	//: TODO: remove this, redundant test code

	function checkSchema($tableName, $dbSchema) {
		global $db;

		$report = '';
		if (false == $db->tableExists($tableName)) { 
			$report .= "[*] Database table '$tableName' is not installed.<br/>\n";
		} else {
			$report .= "[*] Database table '$tableName' exists.<br/>\n";
			$liveSchema = $this->getSchema($tableName);
			if (false == $this->compareSchema($dbSchema, $liveSchema)) {
				$report .= "[*] Table '$tableName' exists but does not match schema.<br/>\n";
			} else {
				$report .= "[*] Table '$tableName' and indices match schema.<br/>\n";
			}
		}
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	create or recreate a table given its schema
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - a database table specification [array]
	//returns: html report of actions taken, false on failure [string]

	function installTable($dbSchema) {
		global $db, $user;
		if ('admin' != $user->role) { return false; }	// only admins can do this
		$report = '';

		if ($db->tableExists($dbSchema['table']) == false) {	
			//--------------------------------------------------------------------------------------
			//	no such table, create it
			//--------------------------------------------------------------------------------------
			$report .= "Creating " . $dbSchema['table'] . " table and indices... ";
			$check = $this->createTable($dbSchema);	
			if (true == $check) { $report .= "<span class='ajaxmsg'>done</span><br/>\n"; }
			else { $report .= "<span class='ajaxerror'>failed</span><br/>\n"; }

		} else {
			//--------------------------------------------------------------------------------------
			//	table exists, check if its up to date
			//--------------------------------------------------------------------------------------
			$report .= $dbSchema['table'] . " table already exists...";	
			$extantSchema = $this->getSchema($dbSchema['table']);	// get specifics of extant table

			if ($this->compareSchema($dbSchema, $extantSchema) == true) {
				$report .= "<span class='ajaxmsg'>all correct</span><br/>";
			} else {
				$check = $this->recreateTable($dbSchema['table'], $dbSchema);
				if (true == $check) { 
					$report .= "<span class='ajaxmsg'>updated to new schema</span><br/>";
				} else {
					$report .= "<span class='ajaxerr'>could not update</span><br/>";
				}
			}
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a report describing a table's install status
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - a database table specification [array]
	//returns: html report of actions taken [string]

	function getTableInstallStatus($dbSchema) {
		global $db, $user;
		if ('admin' != $user->role) { return ''; }

		$installed = true;
		$report = '';

		if ($db->tableExists($dbSchema['table']) == true) {
			//--------------------------------------------------------------------------------------
			//	table present
			//--------------------------------------------------------------------------------------
			$extantSchema = $this->getSchema($dbSchema['table']);

			if ($this->compareSchema($dbSchema, $extantSchema) == false) {
				//----------------------------------------------------------------------------------
				// table schemas DO NOT match (fail)
				//----------------------------------------------------------------------------------
				$installed = false;		
				$report .= "<p>A '" . $dbSchema['table'] . "' table exists, but does not match "
					. "object's schema.</p>\n"
					. "<b>Object Schema:</b><br/>\n"
					. $this->schemaToHtml($dbSchema) . "<br/>\n"
					. "<b>Extant Table:</b><br/>\n"
					. $this->schemaToHtml($extantSchema) . "<br/>\n";

			} else {
				//----------------------------------------------------------------------------------
				// table schemas match
				//----------------------------------------------------------------------------------
				$report .= "<p>'" . $dbSchema['table'] . "' table exists, "
					. "matches object schema.</p>\n"
					. "<b>Database Table:</b><br/>\n"
					. $this->schemaToHtml($dbSchema) . "<br/>\n";

			}

		} else {
			//--------------------------------------------------------------------------------------
			//	table missing (fail)
			//--------------------------------------------------------------------------------------
			$installed = false;
			$report .= "<p>'" . $dbSchema['table'] . "' table does not exist in the database.</p>\n"
				. "<b>Object Schema:</b><br/>\n" . $this->schemaToHtml($dbSchema) . "<br/>\n";
		}
	
		if (true == $installed) { $report .= "<!-- table installed correctly -->"; }
		return $report;
	}

}

?>
