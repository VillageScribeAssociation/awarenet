<?

//-------------------------------------------------------------------------------------------------
//	exports table dbSchemas as php code, for making install script
//-------------------------------------------------------------------------------------------------

if ('admin' != $user->role) { $page->do403(); }
if (('' == $req->ref) || ($db->tableExists($req->ref) == false)) { 
	echo "no table specified";
	die(); 

} else {
	//---------------------------------------------------------------------------------------------
	//	make the code
	//---------------------------------------------------------------------------------------------

	$dbs = xdbGetSchema($req->ref);

	$code = '';
	$code .= "\t//----------------------------------------------------------------------------------------------\n";
	$code .= "\t//\t" . $req->ref . " table\n";
	$code .= "\t//----------------------------------------------------------------------------------------------\n\n";

	$code .= "\t\$dbSchema = array();\n";
	$code .= "\t\$dbSchema['model'] = '" . $req->ref . "';\n";
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
	global $db;

	if ($db->tableExists($tableName) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	create dbSchema array
	//----------------------------------------------------------------------------------------------
	$dbSchema = array(	'table' => $tableName, 'fields' => array(), 
						'indices' => array(), 'nodiff' => array()	);

	//----------------------------------------------------------------------------------------------
	//	add fields
	//----------------------------------------------------------------------------------------------
	$sql = "describe " . $db->addMarkup($tableName);
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) 
		{ $dbSchema['fields'][$row['Field']] = strtoupper($row['Type']); }

	//----------------------------------------------------------------------------------------------
	//	add indices
	//----------------------------------------------------------------------------------------------
	$sql = "show indexes from " . $db->addMarkup($tableName);
	$result = $db->query($sql);
	while ($row = $db->fetchAssoc($result)) 
		{ $dbSchema['indices'][$row['Column_name']] = $row['Sub_part']; }

	return $dbSchema;
}

?>