<? 

//--------------------------------------------------------------------------------------------------
//	record aliases are unique strings used in URLs to identify records and other assets
//--------------------------------------------------------------------------------------------------
//
//	Record aliases and UIDs are equivalent for identifying records, however UIDs should be globally 
//	uniuqe while recordAliases need only be unique to the table.  Their function is to clean URLs 
//	and add keywords from the title into the URL for SEO.
//
//	http://mysite.org/widgets/234987023495 -(301)-> http://mysite.org/widgets/Cat-Polishing-Machine
//
//	Records which cannot use their preferred alias will be assigned another.  RecordAliases are
//	stored in records directly (in contrast to Kapenta 1.0) as well as in a centralised recordalias
//	table, this is so that recordAliases can be changed without breaking existing links, alternate
//	aliases 302 to the default alias (the alias in the record is always default).
//
//	The function raFindRedirect($refTable, $recordAlias) returns the correct UID of a record, or
//	redirects to the default alias if a non-default one is used.

//--------------------------------------------------------------------------------------------------
//	decides on an alias for a record and stores it in the recordalias table, returns UID
//--------------------------------------------------------------------------------------------------
//	Note: this is called by save() methods on records with a recordAlias field, returns the value 
//	for that field.  $plainText is a title field or the like from which a recordAlias is derived.

function raSetAlias($refTable, $refUID, $plainText, $module) {

	//----------------------------------------------------------------------------------------------
	//	get the default recordAlias for this plaintext
	//----------------------------------------------------------------------------------------------
	$default = raFromString($plainText);


	if (trim($default) == '') { 						// no plainText
		$default = $refUID;								// no refUID
		if (trim($default) == '') { return false; } 	// no service
	}
	
	//----------------------------------------------------------------------------------------------
	//	check if record (#refUID) already owns its default recordAlias - if so then we're done
	//----------------------------------------------------------------------------------------------
	$defaultOwner = raGetOwner($default, $refTable);
	
	//echo "default owner: $defaultOwner refUID: $refUID <br/>\n";
	
	if ($defaultOwner == $refUID) { return $default; }
	if ($defaultOwner == false) {
		//------------------------------------------------------------------------------------------
		//	alias is not owned, it can be assigned to this record
		//------------------------------------------------------------------------------------------
		raSaveAlias($refTable, $refUID, $default);
		return $default;
	}

	//----------------------------------------------------------------------------------------------
	//	the default alias is already owned by another record
	//----------------------------------------------------------------------------------------------
	$currAliases = raGetAll($refTable, $refUID);

	if ($currAliases == false) {
		//------------------------------------------------------------------------------------------
		//	this record has no alias yet, find an unused record by appending a number
		//------------------------------------------------------------------------------------------
		$available = raFindAvailable($refTable, $refUID, $default, 0);
		if ($available == false) { return false; }
		raSaveAlias($refTable, $refUID, $available);
		return $available;			

	} else {
		//------------------------------------------------------------------------------------------
		//	the default is owned by another record, return the first alias the record registered
		//------------------------------------------------------------------------------------------
		foreach ($currAliases as $caUID => $caAlias) { return $caAlias; }

	}
	return false; // just in case
}

//--------------------------------------------------------------------------------------------------
//	checks that the alias supplied is the default for the record, redirects to the default if not
//--------------------------------------------------------------------------------------------------

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
//      create a new row in the recordAlias table
//--------------------------------------------------------------------------------------------------

function raSaveAlias($refTable, $refUID, $recordAlias) {
	if ( (trim($refTable) == '')
		 OR (trim($refUID) == '')
		 OR (trim($recordAlias) == '')) { return false; }

	$raUID = createUID();
	$sql = "INSERT INTO recordalias VALUES (" 
			. "'" . $raUID . "', " 
			. "'" . $refTable . "', " 
			. "'" . $refUID . "', " 
			. "'" . strtolower($recordAlias) . "', " 
			. "'" . $recordAlias . "')";
			
	dbQuery($sql);
	return $raUID;
}

//--------------------------------------------------------------------------------------------------
//      convert a string to URI
//--------------------------------------------------------------------------------------------------

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
//	create a record alias, return refURI, eg: This-is---a-Post
//--------------------------------------------------------------------------------------------------

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
		if ($oCC == 32) { $retVal .= '_'; } // space
		if ($oCC == 45) { $retVal .= '-'; } // minus
		if ($oCC == 46) { $retVal .= '.'; } // allow period
	}
	
	return $retVal;
}

//--------------------------------------------------------------------------------------------------
//      redirect browser (and search engines) to latest alias for a record
//--------------------------------------------------------------------------------------------------

function do301($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;	
 	header( "HTTP/1.1 301 Moved Permanently" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
	die();
}

//--------------------------------------------------------------------------------------------------
//      forbidden
//--------------------------------------------------------------------------------------------------

function do403() {
	global $installPath;
 	header( "HTTP/1.1 403 Forbidden" );
	$errPage = new Page($installPath . 'modules/home/actions/403.page.php');
	$errPage->render();
	die();
}

//--------------------------------------------------------------------------------------------------
//      temporary redirect, for shuffling browsers around
//--------------------------------------------------------------------------------------------------

function do302($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;
 	header( "HTTP/1.1 302 Moved Temporarily" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI . "'>here</a>.";
	die();
}

//--------------------------------------------------------------------------------------------------
//      you have died of dysentery
//--------------------------------------------------------------------------------------------------

function do404($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	$errPage = new Page($installPath . 'modules/home/actions/404.page.php');
	$errPage->render();
	die();
}

//--------------------------------------------------------------------------------------------------
//      xml error
//--------------------------------------------------------------------------------------------------

function doXmlError($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	echo "<?xml version=\"1.0\"?>\n";
	echo "<error>$msg</error>\n";
	die();
}

//--------------------------------------------------------------------------------------------------
//      delete all aliases for a record
//--------------------------------------------------------------------------------------------------

function raDeleteAll($refTable, $refUID) {
	$sql = "delete from recordalias "
		 . "where refTable='" . $refTable . "' and refUID='" . $refUID . "'";

	dbQuery($sql);
}

//--------------------------------------------------------------------------------------------------
//      get the default alias of a record (ie, the one in the record itsef)
//--------------------------------------------------------------------------------------------------

function raGetDefault($refTable, $refUID) {
	$sql = "select * from $refTable where UID='" . $refUID . "'";
	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) { 
		$row = dbFetchAssoc($result); 
		if (array_key_exists('recordAlias', $row)) { return $row['recordAlias']; }

	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//      find out which record an alias belongs to
//--------------------------------------------------------------------------------------------------

function raGetOwner($recordAlias, $refTable) {
	$raLC = strtolower($recordAlias);
	$sql = "select * from recordalias "
		 . "where refTable='" . $refTable . "' "
		 . "and aliaslc='" . $raLC . "'";

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) { $row = dbFetchAssoc($result); return $row['refUID']; }
	else { return false; }
}

//--------------------------------------------------------------------------------------------------
//	get all aliases associated with a particular record
//--------------------------------------------------------------------------------------------------

function raGetAll($refTable, $refUID) {
	$aliases = array();
	$sql = "select * from recordalias "
		 . "where refTable='" . $refTable . "' "
		 . "and refUID='" . $refUID . "'";	

	$result = dbQuery($sql);
	if (dbNumRows($result) > 0) {
		while ($row = dbFetchAssoc($result)) { $aliases[$row['UID']] = $row['alias']; }
		return $aliases;
	} else { return false; }
}

//--------------------------------------------------------------------------------------------------
//	find an unused alias which begins $default
//	used to resolve collisions (eg, two Mr-Smith records)
//--------------------------------------------------------------------------------------------------
//	$depth is to prevent infinite recusion if table is full

function raFindAvailable($refTable, $default, $depth) {
	if ($depth > 50) { return false; }
	$extended = $default . '-' . substr(createUID(), 0, 5);
	if (raGetOwner($extended, $refTable) == false) { return $extended; }
	return raFindAvailable($refTable, $default, $depth + 1);
}

?>
