<?

//-------------------------------------------------------------------------------------------------
//	recieve a SQL update from a peer
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	authorize
	//---------------------------------------------------------------------------------------------
	$kapenta->logSync("**** /SYNC/RECVSQL/ ****.<br/>\n");	

	if (false == $sync->authenticate()) {
		$kapenta->logSync("/SYNC/RECVSQL/ could not authenticate.<br/>\n");	
		$page->doXmlError('could not authenticate');
	}

	//---------------------------------------------------------------------------------------------
	//	add to database
	//---------------------------------------------------------------------------------------------
	if (false == array_key_exists('detail', $_POST)) {
		$kapenta->logSync("/SYNC/RECVSQL/ update not sent.<br/>\n");	
		$page->doXmlError('update not sent');
	}
	if ('' == trim($_POST['detail'])) {
		$kapenta->logSync("/SYNC/RECVSQL/ update is empty.<br/>\n");	
		$page->doXmlError('update is empty');
	}
	
	$data = $sync->base64DecodeSql($_POST['detail']);

	if (true == in_array($data['table'], $sync->ignoreTables)) {
		$kapenta->logSync("/SYNC/RECVSQL/ table not synced.<br/>\n");	
		$page->doXmlError('table not synced');
	}

	$kapenta->logSync("received object: " . $data['table'] . " (" . $data['fields']['UID'] . ")\n");
	foreach($data['fields'] as $f => $v) { $kapenta->logSync("field: $f value: $v \n"); }

	$sync->dbSave($data['table'], $data['fields']);

	//---------------------------------------------------------------------------------------------
	//	pass on to peers
	//---------------------------------------------------------------------------------------------	
	//$syncHeaders = $sync->getHeaders();
	//$source = $syncHeaders['Sync-Source'];
	//$sync->broadcastDbUpdate($source, $data['table'], $data['fields']);

	//---------------------------------------------------------------------------------------------
	//	done
	//---------------------------------------------------------------------------------------------		
	echo "<ok/>"; flush();

?>
