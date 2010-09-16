<? 

//--------------------------------------------------------------------------------------------------
//*	record aliases are unique strings used in URLs to identify records and other assets
//--------------------------------------------------------------------------------------------------
//+
//+	Record aliases and UIDs are equivalent for identifying records, however UIDs should be globally 
//+	uniuqe while recordAliases need only be unique to the table.  Their function is to clean URLs 
//+	and add keywords from the title into the URL for SEO.
//+
//+	http://mysite.org/widgets/234987023495 -(301)-> http://mysite.org/widgets/Cat-Polishing-Machine
//+
//+	Records which cannot use their preferred alias will be assigned another.  RecordAliases are
//+	stored in records directly (in contrast to Kapenta 1.0) as well as in a centralised recordalias
//+	table, this is so that recordAliases can be changed without breaking existing links, alternate
//+	aliases 302 to the default alias (the alias in the record is always default).
//+
//+	The function raFindRedirect($refTable, $recordAlias) returns the correct UID of a record, or
//+	redirects to the default alias if a non-default one is used.

//--------------------------------------------------------------------------------------------------
//|	decides on an alias for a record and stores it in the recordalias table, returns UID
//--------------------------------------------------------------------------------------------------
//:	this is called by save() methods on records with a recordAlias field, returns the value 
//:	for that field.  $plainText is a title field or the like from which a recordAlias is derived.
//:	TODO: in future recordAliases should be unique across a module all tables controlled by a 
//:	module rather than unique just to the table the owner record is in.

//arg: refTable - table which contains owner of this alias [string]
//arg: refUID - UID of record which owns this alias [string]
//arg: plainText - URL unfriendly string from which alias is generated [string]
//arg: module - module to which the record belongs
//returns: unique URL friendly record alias unique to table in question or false [string] [bool]

function raSetAlias($refTable, $refUID, $plainText, $module) {
	global $aliases, $session;
	$session->msg("ERROR: Function removed - raSetAlias()", 'bug');
}

//--------------------------------------------------------------------------------------------------
//|	checks that the alias supplied is the default for the record, redirects to the default if not
//--------------------------------------------------------------------------------------------------
//arg: module - name of a module [string]
//arg: action - action to redirect to [string]
//arg: refTable - table which contains the record in question [string]
//arg: recordAlias - an alias of a record [string]
//returns: UID of record if recordAlias is default, dies or redirects in other cases [string]

function raFindRedirect($module, $action, $refTable, $recordAlias) {
	global $serverPath;

	//----------------------------------------------------------------------------------------------
	//	look for this record in the recordalias table
	//----------------------------------------------------------------------------------------------
	$safeAlias = strtolower(sqlMarkup($recordAlias));	
	$sql = "select * from recordalias "
		 . "where (aliaslc='" . $safeAlias . "' or refUID='" . $safeAlias . "') "
		 . "and refTable='" . $refTable . "'";

	$result = dbQuery($sql);


	if (dbNumRows($result) > 0) {
		//------------------------------------------------------------------------------------------
		//	we have a record(s), find the default and compare
		//------------------------------------------------------------------------------------------
		$found = dbFetchAssoc($result);
		$default = raGetDefault($refTable, $found['refUID']);
		if ($default == false) { do404(); }

		if (strtolower($recordAlias) == strtolower($default)) {
			//--------------------------------------------------------------------------------------
			//	default recordAlias was used, return the UID and we're done
			//--------------------------------------------------------------------------------------
			return $found['refUID'];

		} else {
			//--------------------------------------------------------------------------------------
			//	recordAlias used is not default or wrong case (mr-smith -(301)-> Mr-Smith)
			//--------------------------------------------------------------------------------------
			$URI = $module . '/' . $action . '/' . $default;
			$URI = str_replace('//', '/', $URI);
			do301($URI);

		}
	
	} else {
		//------------------------------------------------------------------------------------------
		//	no matches found
		//------------------------------------------------------------------------------------------
		do404('');

	}
}

//--------------------------------------------------------------------------------------------------
//|	create a new row in the recordalias table
//--------------------------------------------------------------------------------------------------
//arg: refTable - table contatining object which owns this alias [string]
//arg: refUID - UID of object which owns this alias [string]
//arg: recordAlias - a record alias [string]
//returns: UID of new recordAlias

function raSaveAlias($refTable, $refUID, $recordAlias) {
	global $db;
	if ( (trim($refTable) == '')
		 OR (trim($refUID) == '')
		 OR (trim($recordAlias) == '')) { return false; }

	$raUID = createUID();

	$data = array(	'UID' => $raUID,
					'refTable' => $refTable,
					'refUID' => $refUID,
					'aliaslc' => strtolower($recordAlias),
					'alias' => $recordAlias,
					'editedOn' => $db->datetime(),
					'editedBy' => $_SESSION['sUserUID']
				);
		
	dbSave($data, raDbSchema());
	return $raUID;
}

//--------------------------------------------------------------------------------------------------
//|	get dbSchema for recordAlias table
//--------------------------------------------------------------------------------------------------
//: this is likely to be incorporated into a model in the future, on recordalias module

function raDbSchema() {
	$dbSchema = array();
	$dbSchema['table'] = 'recordalias';
	$dbSchema['fields'] = array(
		'UID' => 'VARCHAR(30)',		
		'refTable' => 'VARCHAR(100)',
		'refUID' => 'VARCHAR(30)',	
		'aliaslc' => 'VARCHAR(255)',
		'alias' => 'VARCHAR(255)',
		'editedOn' => 'DATETIME',
		'editedBy' => 'VARCHAR(30)'	);

	$dbSchema['indices'] = array('UID' => '10', 'refTable' => '20', 'refUID' => '10', 'aliaslc' => '30');
	// no need to record changes to this table
	$dbSchema['nodiff'] = array('UID', 'refTable', 'refUID', 'aliaslc', 'alias');

	return $dbSchema;
}

//--------------------------------------------------------------------------------------------------
//|	convert a string to URI-friendly label
//--------------------------------------------------------------------------------------------------
//arg: plainText - string from which recordAlias is to be derived [string]
//returns: alias suitable for use in URLs [string]

function raFromString($plainText) {
	$plainText = str_replace("\r", '-', $plainText);
	$plainText = str_replace("\n", '-', $plainText);
	$plainText = str_replace("\t", '-', $plainText);
	$plainText = str_replace(" ", '-', $plainText);
	$plainText = str_replace("_", '-', $plainText);
	$plainText = str_replace("\"", '', $plainText);
	$plainText = str_replace("'", "", $plainText);
	$plainText = str_replace("/", "-fs-", $plainText);
	$plainText = str_replace("&amp;", "-and-", $plainText);
	$plainText = str_replace(";", "", $plainText);
	$plainText = str_replace(":", "-sc-", $plainText);
	$plainText = str_replace("&", "-and-", $plainText);
	$plainText = str_replace("#", "No", $plainText);
	$plainText = str_replace("?", "-", $plainText);
	$plainText = str_replace("---", "-", $plainText);
	$plainText = str_replace("---", "-", $plainText);
	$plainText = str_replace("---", "-", $plainText);
	$plainText = str_replace("--", "-", $plainText);
	$plainText = str_replace("", "n", $plainText);

	$plainText = raAlphaNumeric($plainText);
	$plainText = substr($plainText, 0, 100);

	return $plainText;	
}

//--------------------------------------------------------------------------------------------------
//|	strip non-alphanumeric characters, excluding minus and period, space becomes minus.
//--------------------------------------------------------------------------------------------------
//arg: txt - any string [string]
//returns: only those characters suitable for use in URLs [string]

function raAlphaNumeric($txt) {
	$retVal = '';
	$txt = trim($txt);
	if ($txt == '') { return ''; }
	$numChars = strlen($txt);
	for($i = 0; $i < $numChars; $i++) {
		$currChar = substr($txt, $i, 1);
		$oCC = ord($currChar);
		
		if (($oCC >= 48) AND ($oCC <= 57)) { $retVal .= $currChar; } 	// 0-9
		if (($oCC >= 97) AND ($oCC <= 122)) { $retVal .= $currChar; } 	// a-z
		if (($oCC >= 65) AND ($oCC <= 90)) { $retVal .= $currChar; } 	// A-Z
		if ($oCC == 32) { $retVal .= '-'; } // space
		if ($oCC == 45) { $retVal .= '-'; } // minus
		if ($oCC == 46) { $retVal .= '.'; } // allow period
	}
	
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//|	delete all aliases for a record
//--------------------------------------------------------------------------------------------------
//arg: refTable - table which contains object identified by refUID [string]
//arg: refUID - UID of object which may own recordalias(es) [string]

function raDeleteAll($refTable, $refUID) {
	$sql = "select * from recordalias " 
		 . "where refTable='" . sqlMarkup($refTable) . "' "
		 . "and refUID='" . sqlMarkup($refUID) . "'";

	$result = dbQuery($sql);
	while ($row = dbFetchAssoc($result)) { dbDelete('recordalias', $row['UID']); }
}

//--------------------------------------------------------------------------------------------------
//|	get the default alias of a record (ie, the one in the record itsef)
//--------------------------------------------------------------------------------------------------
//arg: refTable - table which contains object identified by refUID [string]
//arg: refUID - UID of object which may own recordalias(es) [string]

function raGetDefault($refTable, $refUID) {
	$sql = "select * from " . sqlMarkup($refTable) . " where UID='" . sqlMarkup($refUID) . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) { 
		$row = dbFetchAssoc($result); 
		$row = sqlRMArray($row);
		if (array_key_exists('recordAlias', $row)) { return $row['recordAlias']; }

	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	find out which record an alias belongs to
//--------------------------------------------------------------------------------------------------
//arg: recordAlias - a recordalias (case insensitive) [string]
//arg: refTable - table containing object which owns this alias [string]
//returns: UID of object which owns this alias or false on failure [string] [bool]

function raGetOwner($recordAlias, $refTable) {
	$sql = "select * from recordalias "
		 . "where refTable='" . sqlMarkup($refTable) . "' "
		 . "and aliaslc='" . sqlMarkup(strtolower($recordAlias)) . "'";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) { 
		$row = dbFetchAssoc($result); 
		$row = sqlRMArray($row);
		return $row['refUID']; 

	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	get all aliases associated with a particular record
//--------------------------------------------------------------------------------------------------
//arg: refTable - table which contains object identified by refUID [string]
//arg: refUID - UID of object which may own recordalias(es) [string]

function raGetAll($refTable, $refUID) {
	$aliases = array();
	$sql = "select * from recordalias "
		 . "where refTable='" . sqlMarkup($refTable) . "' "
		 . "and refUID='" . sqlMarkup($refUID) . "'";	

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) { 
			$row = sqlRMArray($row);
			$aliases[$row['UID']] = $row['alias']; 
		}
		return $aliases;
	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//|	find an unused alias which begins $default
//--------------------------------------------------------------------------------------------------
//: this function is used to resolve collisions (eg, two Mr-Smith records)
//: set depth to 0 when calling

//arg: refTable - table containing owner record
//arg: default - the recordAlias owner object would prefer, but which is not available [string]
//arg: depth - to prevent infinite recusion if table is full [int]

function raFindAvailable($refTable, $default, $depth) {
	if ($depth > 50) { return false; }
	$extended = $default . '-' . substr(createUID(), 0, 5);
	if (raGetOwner($extended, $refTable) == false) { return $extended; }
	return raFindAvailable($refTable, $default, $depth + 1);
}

?>
