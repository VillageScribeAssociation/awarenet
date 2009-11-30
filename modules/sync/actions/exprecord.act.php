<?

//-------------------------------------------------------------------------------------------------
//	export a record given table and UID
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authenticate
	//---------------------------------------------------------------------------------------------
	//if (syncAuthenticate() == false) { doXmlError('could not authenticate'); }
	//logSync("authenticated notice reciept\n");

	//---------------------------------------------------------------------------------------------
	//	check the record exists
	//---------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { doXmlError('record not specified unspecified'); }
	if (array_key_exists('table', $request['args']) == false) { doXmlError('table unspecified'); }

	$getTable = $request['args']['table'];
	$getUid = $request['ref'];

	$tables = dbListTables();
	if (in_array($getTable, $tables) == false) { doXmlError('no such table'); }
	if (dbRecordExists($getTable, $getUid) == false) { doXmlError('no such record'); }

	//---------------------------------------------------------------------------------------------
	//	load and return the record
	//---------------------------------------------------------------------------------------------
	$data = dbLoad($getTable, $getUid);
	$xml = syncBase64EncodeSql($getTable, $data);
	echo $xml;

?>
