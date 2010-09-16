<?

//-------------------------------------------------------------------------------------------------
//	export a list of records in a table (assuming it has the right field - UID, editedBy, editedOn)
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	check auth
	//---------------------------------------------------------------------------------------------
	
	//if (false == $sync->authenticate()) { $page->doXmlError('authentication not valid'); }
	$dba = new KDBAdminDriver();	

	//---------------------------------------------------------------------------------------------
	//	check and load the table
	//---------------------------------------------------------------------------------------------
	
	if ('' == $req->ref) { $page->doXmlError('no table specified'); }

	$tables = $dba->listTables();
	if (false == in_array($req->ref, $tables)) { $page->doXmlError('unknown table'); }

	$dbSchema = $dba->getSchema($req->ref);
	if ( (array_key_exists('UID', $dbSchema['fields']) == false) 
		|| (array_key_exists('editedOn', $dbSchema['fields']) == false)
		|| (array_key_exists('editedBy', $dbSchema['fields']) == false) ) 
		{ $page->doXmlError('table must contain UID, editedBy, editedOn'); }

	//---------------------------------------------------------------------------------------------
	//	load and export record list 
	//---------------------------------------------------------------------------------------------
	
	$rows = $db->loadRange($req->ref, 'UID, editedBy, editedOn', '', '', '', '');

	if ((true == array_key_exists('sha1',$req->args)) && ('yes' == $req->args['sha1'])) {
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
