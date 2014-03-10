<?

//--------------------------------------------------------------------------------------------------
//	serve a file from the repository base64_encoded
//--------------------------------------------------------------------------------------------------

	if ($kapenta->request->ref != '') { $kapenta->request->args['file'] = $kapenta->request->ref; }
	if (array_key_exists('file', $kapenta->request->args) == false) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	// load the record
	//----------------------------------------------------------------------------------------------
	$sql = "select * from code where UID='" . $db->addMarkup($kapenta->request->args['file']) . "'";
	$result = $db->query($sql);	
	if ($db->numRows($result) == 0) { $page->do404(); }
	$row = $db->fetchAssoc($result);
	$row = $db->rmArray($row);

	$content = base64wrap($row['content']);

	//----------------------------------------------------------------------------------------------
	// check for binary
	//----------------------------------------------------------------------------------------------
	$binFile = $kapenta->installPath . 'data/code/binaries/' . $row['UID'] . '.xxx';
	if ((file_exists($binFile) == true) && ($row['content'] == '(binary file attached)')) { 
			$content = implode(file($binFile)); 
			$content = base64wrap($content);
	}	

	//----------------------------------------------------------------------------------------------
	// send to client
	//----------------------------------------------------------------------------------------------
	header("Content-Length: " . strlen($content));
	echo $content;

//--------------------------------------------------------------------------------------------------
//	util
//--------------------------------------------------------------------------------------------------

function base64wrap($txt) {
	$retVal = '';
	while (strlen($txt) > 80) {
		$retVal .= substr($txt, 0, 80) . "\n";		
		$txt = substr($txt, 80);
	}
	return $retVal . $txt;
}

?>
