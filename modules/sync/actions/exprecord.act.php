<?

//-------------------------------------------------------------------------------------------------
//	export a record given table and UID
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authenticate
	//---------------------------------------------------------------------------------------------
	//if ($sync->authenticate() == false) { $page->doXmlError('could not authenticate'); }
	//$kapenta->logSync("authenticated notice reciept\n");

	//---------------------------------------------------------------------------------------------
	//	check the record exists
	//---------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->doXmlError('record not specified unspecified'); }
	if (false == array_key_exists('table', $req->args)) { $page->doXmlError('table unspecified'); }

	$tableName = str_replace('-us-', '_', $req->args['table']);
	$tables = $db->loadTables();
	foreach($tables as $table) { if (strtolower($table) == $tableName) { $tableName = $table; }	}
	if (false == in_array($tableName, $tables)) { $page->doXmlError('unknown table: '.$tableName); }

	$getUid = $req->ref;
	
	if (false == $db->objectExists($tableName, $getUid)) { $page->doXmlError('no such record'); }

	//---------------------------------------------------------------------------------------------
	//	load and return the record
	//---------------------------------------------------------------------------------------------
	$dbSchema = $db->getSchema($tableName);

	$data = $db->load($getUid, $dbSchema);
	$xml = $sync->base64EncodeSql($tableName, $data);
	echo $xml;

?>
