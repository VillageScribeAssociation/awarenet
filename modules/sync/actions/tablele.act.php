<?

//-------------------------------------------------------------------------------------------------
//*	export a list of records in a table (assuming it has the right field - UID, editedBy, editedOn)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check auth
	//---------------------------------------------------------------------------------------------
	
	//if (false == $sync->authenticate()) { $page->doXmlError('authentication not valid'); }
	$dba = new KDBAdminDriver();	

	//---------------------------------------------------------------------------------------------
	//	check table exists and get its schema
	//---------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->doXmlError('no table specified'); }
	$tableName = str_replace('-us-', '_', $req->ref);
	$tables = $db->loadTables();
	foreach($tables as $table) { if (strtolower($table) == $tableName) { $tableName = $table; }	}
	if (false == in_array($tableName, $tables)) { $page->doXmlError('unknown table: '.$tableName); }

	$dbSchema = $db->getSchema($tableName);
	if ( (false == array_key_exists('UID', $dbSchema['fields'])) 
		|| (array_key_exists('editedOn', $dbSchema['fields']) == false)
		|| (array_key_exists('editedBy', $dbSchema['fields']) == false) ) 
		{ $page->doXmlError('table must contain UID, editedBy, editedOn'); }

	//---------------------------------------------------------------------------------------------
	//	load and export record list 
	//---------------------------------------------------------------------------------------------
	
	$range = $db->loadRange($tableName, 'UID, editedOn', '');

	if ((true == array_key_exists('sha1',$req->args)) && ('yes' == $req->args['sha1'])) {
		//-----------------------------------------------------------------------------------------
		//  send only sha1 hash of list (TODO: think of faster way to make hash)
		//-----------------------------------------------------------------------------------------
		$list = '';
		foreach($range as $row => $fields) {	$list .= implode('|', $fields) . "\n"; }
		echo sha1($list);
	
	} else {
		//-----------------------------------------------------------------------------------------
		//  print complete list (pipe and newline delimited)
		//-----------------------------------------------------------------------------------------
		foreach($range as $row => $fields) {	echo implode('|', $fields) . "\n"; }

	}

?>
