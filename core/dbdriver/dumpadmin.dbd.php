<?

//--------------------------------------------------------------------------------------------------
//*	administrative database functions for mysql
//--------------------------------------------------------------------------------------------------
//+	This is a partial implementation / stub driver to aloow dumping of the database to a text 
//+	document.
//+
//+ Table schema are passed as nested associative arrays:
//+
//+  'table' -> string, name of table
//+  'fields' -> array of fieldname -> type, where type is MySQL type
//+  'indices' -> array of fieldname -> size (index name is derived from fieldname)
//+	 'nodiff' -> array of field names which are not versioned (eg, hitcount)


class KDBAdminDriver_Dump {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $db;				//_	database connection to use [object]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KDBAdminDriver_Dump($dbd) {
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
		$sql = 'USE ' . $this->db->name;
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

		$sql = ''
		 . "DROP TABLE IF EXISTS `" . $table . "`; "
		 . "CREATE TABLE `" . $table . "`"
		 . " (\n" . implode(",\n", $fields) . $prikey . ")"
		 . " DEFAULT CHARACTER SET 'utf8'"
		 . ";\n";

		$this->db->query($sql);

		$this->db->loadTables();
		if (false == $this->db->tableExists($table)) { return false; }	// check it worked

		//------------------------------------------------------------------------------------------
		//	create indices
		//------------------------------------------------------------------------------------------
		foreach($dbSchema['indices'] as $idxField => $idxSize) {
			$idxName = 'idx' . $table . $idxField;
			$sql = "CREATE INDEX $idxName ON $table ($idxField($idxSize));";
			if ('' == $idxSize) { $sql = "CREATE INDEX $idxName ON $table ($idxField);"; }
			$this->db->query($sql);
		}

		//------------------------------------------------------------------------------------------
		//	check that indices were created correctly
		//------------------------------------------------------------------------------------------
		$indexes = $this->getIndexes($dbSchema['model']);
		if (false === $indexes) { return false; }
		foreach($dbSchema['indices'] as $idxField => $idxSize) {
			$idxName = 'idx' . $dbSchema['model'] . $idxField;
			$found = false;
			foreach($indexes as $index) {
				if ($index['Key_name'] == $idxName) { $found = true; }
			}
			if (false == $found) { $session->msgAdmin('Could not make index: ' . $idxName, 'bad'); }
		}

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a list of indexes on a table
	//----------------------------------------------------------------------------------------------
	//arg: tableName - name of a table in the default database [string]
	//returns: array of index data, or false on failure [array][bool]

	function getIndexes($tableName) { return false; }

	//----------------------------------------------------------------------------------------------
	//.	copy all records from one table to another
	//----------------------------------------------------------------------------------------------
	//arg: fromTable - name of database table to copy from [string]
	//arg: toSchema - name of database table to copy to [array]
	//arg: rename - map of fields to rename (old => new) [array]
	//returns: number of records copied [int]

	function copyAll($fromTable, $toSchema, $rename) { return 0; }

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
	//: used when changing database schema
	//arg: tableName - name of a database table / model name [string]
	//arg: dbSchema - a database table schema [array]
	//returns: true on success, false if it fails [bool]

	function recreateTable($tableName, $dbSchema) { return false; }

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
			$report .= "[*] Not supported: this is a stub/export driver.<br/>\n";
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
		$report = "<span class='ajaxerr'>Not supported: this is a stub/export diver.</span><br/>";
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a report describing a table's install status
	//----------------------------------------------------------------------------------------------
	//arg: dbSchema - a database table specification [array]
	//returns: html report of actions taken [string]

	function getTableInstallStatus($dbSchema) {
		$report = "<span class='ajaxerr'>N/A.</span><br/>";
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//.	find an object in the database given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - Unique identifier of any object [string]
	//returns: name of table containing object, false if not found [string][bool]

	function findByUID($UID) { return false; }

}

?>
