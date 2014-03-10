<?

//-------------------------------------------------------------------------------------------------
//	exports table dbSchemas as php code, for making install script
//-------------------------------------------------------------------------------------------------

if ('admin' != $user->role) { $kapenta->page->do403(); }
if (('' == $kapenta->request->ref) || ($kapenta->db->tableExists($kapenta->request->ref) == false)) { 
	echo "no table specified";
	die(); 

} else {
	//---------------------------------------------------------------------------------------------
	//	make the code
	//---------------------------------------------------------------------------------------------

	$dbs = xdbGetSchema($kapenta->request->ref);

	$code = '';
	$code .= "\t//----------------------------------------------------------------------------------------------\n";
	$code .= "\t//\t" . $kapenta->request->ref . " table\n";
	$code .= "\t//----------------------------------------------------------------------------------------------\n\n";

	$code .= "\t\$dbSchema = array();\n";
	$code .= "\t\$dbSchema['model'] = '" . $kapenta->request->ref . "';\n";
	$code .= "\t\$dbSchema['fields'] = array(\n";

	foreach($dbs['fields'] as $field => $type) {
		$code .= "\t\t'" . $field . "' => '" . $type . "',\n";
	}

	$code .= ");\n";
	$code = str_replace(",\n);", " );\n", $code);


	echo "<html>
	<body>
	<textarea rows='50' cols='140'>$code</textarea>
	</body>
	</html>
	";

}


function xdbGetSchema($tableName) {
	global $kapenta;

	if ($kapenta->db->tableExists($tableName) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	create dbSchema array
	//----------------------------------------------------------------------------------------------
	$dbSchema = array(	'table' => $tableName, 'fields' => array(), 
						'indices' => array(), 'nodiff' => array()	);

	//----------------------------------------------------------------------------------------------
	//	add fields
	//----------------------------------------------------------------------------------------------
	$sql = "describe " . $kapenta->db->addMarkup($tableName);
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) 
		{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

	//----------------------------------------------------------------------------------------------
	//	add indices
	//----------------------------------------------------------------------------------------------
	$sql = "show indexes from " . $kapenta->db->addMarkup($tableName);
	$result = $kapenta->db->query($sql);
	while ($row = $kapenta->db->fetchAssoc($result)) 
		{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

	return $dbSchema;
}

?>