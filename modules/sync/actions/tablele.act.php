<?

//-------------------------------------------------------------------------------------------------
//	export a list of records in a table (assuming it has the right field - UID, editedBy, editedOn)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check auth
	//---------------------------------------------------------------------------------------------
	
	//if (syncAuthenticate() == false) { doXmlError('authentication not valid'); }
	
	//---------------------------------------------------------------------------------------------
	//	check and load the table
	//---------------------------------------------------------------------------------------------
	
	if ($request['ref'] == '') { doXmlError('no table specified'); }

	$tables = dbListTables();
	if (in_array($request['ref'], $tables) == false) { doXmlError('unknown table'); }

	$dbSchema = dbGetSchema($request['ref']);
	if ( (array_key_exists('UID', $dbSchema['fields']) == false) 
		|| (array_key_exists('editedOn', $dbSchema['fields']) == false)
		|| (array_key_exists('editedBy', $dbSchema['fields']) == false) ) 
		{ doXmlError('table must contain UID, editedBy, editedOn'); }

	//---------------------------------------------------------------------------------------------
	//	load and export record list 
	//---------------------------------------------------------------------------------------------
	
	$rows = dbLoadRange($request['ref'], 'UID, editedBy, editedOn', '', '', '', '');

	if ( (array_key_exists('sha1',$request['args']) == true)
		 && ($request['args']['sha1'] == 'yes') ) {

		//-----------------------------------------------------------------------------------------
		//  send only sha1 hash of list (TODO: think of faster way to make hash)
		//-----------------------------------------------------------------------------------------
		$list = '';
		foreach($rows as $row => $fields) {	$list .= implode('|', $fields) . "\n"; }
		echo sha1($list);
	
	} else {
		//-----------------------------------------------------------------------------------------
		//  print complete list (pipe and newline delimited)
		//-----------------------------------------------------------------------------------------
		foreach($rows as $row => $fields) {	echo implode('|', $fields) . "\n"; }

	}

?>
