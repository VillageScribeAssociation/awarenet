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


class KDBAdminDriver_SQLite {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $db;				//_	database connection to use [object]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KDBAdminDriver_SQLite($dbd) {
		$this->db = $dbd;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a database (consider removing this, live sites should not need this functionality)
	//----------------------------------------------------------------------------------------------
	//arg: database - name of database [string]
	//returns: true on success or false on error [bool]

	function create($database) {
		$this->db->name = $database;
		if ('admin' != $user->role) { return false; }
		$sql = 'CREATE TABLE IF NOT EXISTS test_canary';
		$result = $this->db->query($sql);
		if (false == $result) { return false; }
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make a table + indices given dbSchema
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - table schema [array]
	//returns: true on success, false on failure [bool]

	function createTable($dbSchema) {
		global $user, $session;

		//------------------------------------------------------------------------------------------
		//	checks
		//------------------------------------------------------------------------------------------
		if (true == $this->db->tableExists($dbSchema['model'])) {
			echo "Table already exists...<br/>\n";
			return false;
		}

		$table = $dbSchema['model'];

		//------------------------------------------------------------------------------------------
		//	check for a primary key
		//------------------------------------------------------------------------------------------
		$prikey = '';
		if (true == array_key_exists('prikey', $dbSchema)) {
			if (true == array_key_exists($dbSchema['prikey'], $dbSchema['fields'])) {
				$prikey = ", PRIMARY KEY (" . $dbSchema['prikey'] . ")";
				$dbSchema['fields'][$dbSchema['prikey']] .= " NOT NULL AUTO_INCREMENT";
			} else {
				$session->msgAdmin("Primary key not present.");
			}
		}

		//------------------------------------------------------------------------------------------
		//	create the table
		//------------------------------------------------------------------------------------------
		$fields = array();
		foreach($dbSchema['fields'] as $fName => $fType) { $fields[] = '  '. $fName .' '. $fType; }
		$sql = "CREATE TABLE " . $table . " (\n" . implode(",\n", $fields) . $prikey . ");\n";
		$this->db->query($sql);

		$this->db->loadTables();
		if (false == $this->db->tableExists($table)) { return false; }	// check it worked

		//------------------------------------------------------------------------------------------
		//	create indices
		//------------------------------------------------------------------------------------------
		//	note that SQLite does not have a concept of index size, but does require an index order
		//	in some cases.
	
		foreach($dbSchema['indices'] as $idxField => $idxSize) {
			$idxName = 'idx' . $table . $idxField;
			$sql = "CREATE INDEX IF NOT EXISTS $idxName ON $table ($idxField ASC);";
			$result = $this->db->query($sql);
		}

		//------------------------------------------------------------------------------------------
		//	check that indices were created correctly
		//------------------------------------------------------------------------------------------
		$indexes = $this->getIndexes($dbSchema['model']);
		if (false === $indexes) { return false; }
		foreach($dbSchema['indices'] as $idxField => $idxSize) {
			$idxName = 'idx' . $dbSchema['model'] . $idxField;
			$found = false;

			foreach($indexes as $index) { if ($index['name'] == $idxName) { $found = true; } }
			if (false == $found) { $session->msgAdmin('Could not make index: ' . $idxName, 'bad'); }
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a list of indexes on a table
	//----------------------------------------------------------------------------------------------
	//arg: tableName - name of a table in the default database [string]
	//returns: array of index data, or false on failure [array][bool]

	function getIndexes($tableName) {
		global $db;		
		$indexes = array();		//	return value [array]
		if (false == $this->db->tableExists($tableName)) { return false; }

		//$sql = "SHOW INDEXES FROM " . $tableName;
		$sql = "PRAGMA index_list($tableName);";

		$result = $this->db->query($sql);
		while($row = $this->db->fetchAssoc($result)) { $indexes[] = $row;	}
		return $indexes;
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

		if (false == $this->db->tableExists($fromTable)) { return false; }
		if (false == $this->db->tableExists($toSchema['model'])) { return false; }		

		$endTrans = $db->transactionStart();

		$result = $db->query("SELECT * FROM `" . $fromTable . "`");

		while($row = $db->fetchAssoc($result)) {

			$row = $db->rmArray($row);

			if (false == $this->db->objectExists($toSchema['model'], $row['UID'])) {
				$newObj = array();

				// default values
				foreach($toSchema['fields'] as $fName => $fType) {
					switch(strtolower($fType)) {
						case 'bigint':		$newObj[$fName] = '0';	break;
						case 'int':			$newObj[$fName] = '0';	break;
						case 'float':		$newObj[$fName] = '0';	break;
						case 'datetime':	$newObj[$fName] = $this->db->datetime();	break;
						default:			$newObj[$fName] = '';	break;
					}
					if (true == array_key_exists($fName, $row)) { $newObj[$fName] = $row[$fName]; }
				}

				// rename fields
				foreach($rename as $fromName => $toName) { $newObj[$toName] = $row[$fromName]; }

				$this->db->save($newObj, $toSchema);	
				$count++;
			}
		}

		if (true == $endTrans) { $endTrans = $db->transactionEnd(); }

		return $count;
	}

	//----------------------------------------------------------------------------------------------
	//.	return a dbSchema array as html
	//----------------------------------------------------------------------------------------------
	//: this is mostly for debugging and administrative display
	//arg: dbSchema - a database table schema [array]
	//returns: HTML table with kapenta 'scaffold' style [string]

	function schemaToHtml($dbSchema, $title = '') {
		global $theme, $session;

		if ('' == $title) { $title = $dbSchema['model'] . " (dbSchema)"; }		// default title
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
		if ($dbSchema1['model'] != $dbSchema2['model']) { return false; }
		$tStr1 = ''; $tStr2 = '';

		//------------------------------------------------------------------------------------------
		//	fields must be in same order
		//------------------------------------------------------------------------------------------
		foreach($dbSchema1['fields'] as $key => $val) { $tStr1 .= $key . '|' . $val . "\n"; }
		foreach($dbSchema2['fields'] as $key => $val) { $tStr2 .= $key . '|' . $val . "\n"; }
		$tStr1 = str_replace('VARCHAR(', 'CHAR(', $tStr1);
		$tStr2 = str_replace('VARCHAR(', 'CHAR(', $tStr2);
		if ($tStr1 != $tStr2) { return false; }

		//------------------------------------------------------------------------------------------
		//	indices need not be in same order
		//------------------------------------------------------------------------------------------
		//	Note that index size cannot be compared on SQLite

		foreach($dbSchema1['indices'] as $field => $size) {
			if (array_key_exists($field, $dbSchema2['indices']) == false) { return false; }
		}

		foreach($dbSchema2['indices'] as $field => $size) {
			if (array_key_exists($field, $dbSchema1['indices']) == false) { return false; }
		}

		return true;	// nothing turned out to be incorrect
	}

	//----------------------------------------------------------------------------------------------
	//.	recreate a table to a given schema
	//----------------------------------------------------------------------------------------------
	//: used when changing database schema
	//arg: tableName - name of a database table / model name [string]
	//arg: dbSchema - a database table schema [array]
	//returns: true on success, false if it fails [bool]

	function recreateTable($tableName, $dbSchema) {
		global $kapenta, $db, $user, $session;
		if ($user->role != 'admin') { return false; }	// only admins can do this

		//------------------------------------------------------------------------------------------
		//	create a new, temporary table according to schema without indices and revisioning
		//------------------------------------------------------------------------------------------

		$tmpSchema = array(
			'module' => '',
			'model' => 'tmp_' . $tableName . '_' . $kapenta->createUID(),
			'fields' => array(),
			'indices' => array(),
			'archive' => 'no'
		);

		foreach($dbSchema['fields'] as $fName => $fType) { $tmpSchema['fields'][$fName] = $fType; }
		foreach($dbSchema['fields'] as $fName => $size) { $tmpSchema['nodiff'][] = $fName; }

		$check = $this->createTable($tmpSchema);
		if (false == $check) { 
			$session->msgAdmin("Could not create temp table: " . $tmpSchema['model'], 'bad');
			return false;
		}

		//------------------------------------------------------------------------------------------
		//	copy all records into temp table
		//------------------------------------------------------------------------------------------
		$sql = 'SELECT * FROM ' . $tableName;
		$result = $this->db->query($sql);
		//TODO: more intelligence here

		$this->db->transactionStart();

		while ($row = $this->db->fetchAssoc($result)) { 
			$row = $this->db->rmArray($row);
			foreach($dbSchema['fields'] as $fName => $fType) {
				switch(strtolower($fType)) {
					case 'bigint':
						if (false == array_key_exists($fName, $row)) { $row[$fName] = '0'; }
						if ('' == $row[$fName]) { $row[$fName] = '0'; }
						break;

					case 'bigint(20)':
						if (false == array_key_exists($fName, $row)) { $row[$fName] = '0'; }
						if ('' == $row[$fName]) { $row[$fName] = '0'; }
						break;

				}
			}
			$this->db->save($row, $tmpSchema, false, false, false); 
		}

		$this->db->transactionEnd();

		//------------------------------------------------------------------------------------------
		//	delete the original table and its indices
		//------------------------------------------------------------------------------------------
		$indexes = $this->getIndexes($tableName);
		if ($indexes != false) {
			foreach($indexes as $row) { 
				$this->db->query("DROP INDEX " . $row['name']); 
			}
		}

		$this->db->query('DROP TABLE ' . $tableName);
		$this->db->loadTables();

		//------------------------------------------------------------------------------------------
		//	create new table with indices and copy all records from temporary table
		//------------------------------------------------------------------------------------------
		$this->createTable($dbSchema);
		$sql = "select * from " . $tmpSchema['model'];
		$result = $this->db->query($sql);
		while ($row = $this->db->fetchAssoc($result)) { 
			$row = $this->db->rmArray($row);
			$this->db->save($row, $dbSchema); 
		}

		//------------------------------------------------------------------------------------------
		//	delete the temp table
		//------------------------------------------------------------------------------------------
		//$this->db->query('DROP TABLE ' . $tmpSchema['model']);
		//$this->db->loadTables();

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
		if (false == $this->db->tableExists($tableName)) { 
			$report .= "[*] Database table '$tableName' is not installed.<br/>\n";
		} else {
			$report .= "[*] Database table '$tableName' exists.<br/>\n";
			$liveSchema = $this->db->getSchema($tableName);
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

		if ($this->db->tableExists($dbSchema['model']) == false) {	
			//--------------------------------------------------------------------------------------
			//	no such table, create it
			//--------------------------------------------------------------------------------------
			$report .= "Creating " . $dbSchema['model'] . " table and indices... ";
			$check = $this->createTable($dbSchema);	
			if (true == $check) { $report .= "<span class='ajaxmsg'>done</span><br/>\n"; }
			else { $report .= "<span class='ajaxerror'>failed</span><br/>\n"; }

		} else {
			//--------------------------------------------------------------------------------------
			//	table exists, check if its up to date
			//--------------------------------------------------------------------------------------
			$report .= $dbSchema['model'] . " table already exists...";	
			$extantSchema = $this->db->getSchema($dbSchema['model']);	// get specifics of extant table

			if ($this->compareSchema($dbSchema, $extantSchema) == true) {
				$report .= "<span class='ajaxmsg'>all correct</span><br/>";
			} else {
				$check = $this->recreateTable($dbSchema['model'], $dbSchema);
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

		if ($this->db->tableExists($dbSchema['model']) == true) {
			//--------------------------------------------------------------------------------------
			//	table present
			//--------------------------------------------------------------------------------------
			$extantSchema = $this->db->getSchema($dbSchema['model']);

			if ($this->compareSchema($dbSchema, $extantSchema) == false) {
				//----------------------------------------------------------------------------------
				// table schemas DO NOT match (fail)
				//----------------------------------------------------------------------------------
				$installed = false;		
				$report .= "<p>A '" . $dbSchema['model'] . "' table exists, but does not match "
					. "object's schema.</p>\n"
					. "<b>Object Schema:</b><br/>\n"
					. $this->schemaToHtml($dbSchema) . "<br/>\n"
					. "<b>Extant Table:</b><br/>\n"
					. $this->schemaToHtml($extantSchema) . "<br/>\n";

			} else {
				//----------------------------------------------------------------------------------
				// table schemas match
				//----------------------------------------------------------------------------------
				$report .= "<p>'" . $dbSchema['model'] . "' table exists, "
					. "matches object schema.</p>\n"
					. "<b>Database Table:</b><br/>\n"
					. $this->schemaToHtml($dbSchema) . "<br/>\n";

			}

		} else {
			//--------------------------------------------------------------------------------------
			//	table missing (fail)
			//--------------------------------------------------------------------------------------
			$installed = false;
			$report .= "<p>'" . $dbSchema['model'] . "' table does not exist in the database.</p>\n"
				. "<b>Object Schema:</b><br/>\n" . $this->schemaToHtml($dbSchema) . "<br/>\n";
		}
	
		if (true == $installed) { $report .= "<!-- table installed correctly -->"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	find an object in the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - Unique identifier of any object [string]
	//returns: name of table containing object, false if not found [string][bool]

	function findByUID($UID) {
		global $db;

		$tables = $this->db->loadTables();
		foreach($tables as $tableName) { 
			if (true == $this->db->objectExists($tableName, $UID)) { return $tableName; }
		}

		return false;
	}

}

?>
