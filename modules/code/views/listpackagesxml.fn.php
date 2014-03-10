<?

//--------------------------------------------------------------------------------------------------
//|	makes an XML package list
//--------------------------------------------------------------------------------------------------

function code_listpackagesxml($args) {
	global $kapenta;
	global $db;

	$xml = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: allow private / restricted packages

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	//	^add any restrictions here

	$range = $db->loadRange('code_package', '*', '', 'name ASC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$xml .= "<source>\n";
	$xml .= "\t<url>" . $kapenta->serverPath . "code/</url>\n";
	$xml .= "\t<checked>" . $db->datetime() . "</checked>\n";
	$xml .= "\t<packages>\n";

	foreach($range as $item) {
		$xml .= ''
		 . "\t\t<package>\n"
		 . "\t\t\t<uid>" . $item['UID'] . "</uid>\n"
		 . "\t\t\t<name>" . $item['name'] . "</name>\n"
		 . "\t\t\t<version>" . $item['version'] . "</version>\n"
		 . "\t\t\t<revision>" . $item['revision'] . "</revision>\n"
		 . "\t\t\t<description>" . str_replace('&', '&amp;', $item['description']) . "</description>\n"
		 . "\t\t\t<updated>" . $item['editedOn'] . "</updated>\n"
		 . "\t\t</package>\n";
	}

	$xml .= "\t</packages>\n";
	$xml .= "</source>\n";

	return $xml;
}

?>
