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

	$getTable = $req->args['table'];
	$getUid = $req->ref;

	$tables = $db->listTables();
	if (false == in_array($getTable, $tables)) { $page->doXmlError('no such table'); }
	if (fase == $db->objectExists($getTable, $getUid)) { $page->doXmlError('no such record'); }

	//---------------------------------------------------------------------------------------------
	//	load and return the record
	//---------------------------------------------------------------------------------------------
	$data = $db->load($getTable, $getUid);
	$xml = $sync->base64EncodeSql($getTable, $data);
	echo $xml;

?>
