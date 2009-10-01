<?

//--------------------------------------------------------------------------------------------------
//	images API, no public actions
//--------------------------------------------------------------------------------------------------

if ($user->data['ofGroup'] == 'public') { doXmlError('not logged in'); }

//--------------------------------------------------------------------------------------------------
//	list images 
//--------------------------------------------------------------------------------------------------

if ($request['ref'] == 'list') {
	$sql = "select * from images where 1=1 ";

	if (array_key_exists('refuid', $request['args']) == true) 
		{ $sql .= " and refUID='" . sqlMarkup($request['args']['refuid']) . "'"; }

	if (array_key_exists('refmodule', $request['args']) == true) 
		{ $sql .= " and refModule='" . sqlMarkup($request['args']['refmodule']) . "'"; }

	$result = dbQuery($sql);
	echo "<?xml version=\"1.0\"?>\n";
	echo "<imageset>\n";
	while ($row = sqlRMArray(dbFetchAssoc($result))) { echo arrayToXml2d($row, 'image', '  '); }
	echo "</imageset>\n";
}

?>
