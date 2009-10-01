<?

//--------------------------------------------------------------------------------------------------
//	API of galleries module. No public actions.
//--------------------------------------------------------------------------------------------------

if ($user->data['ofGroup'] == 'public') { doXmlError('not logged in'); }

//--------------------------------------------------------------------------------------------------
//	list records owned by the current user
//--------------------------------------------------------------------------------------------------

if ($request['ref'] == 'myrecords') {
	$sql = "select * from gallery where createdBy='" . $user->data['UID'] . "' order by title";
	$result = dbQuery($sql);
	
	echo "<?xml version=\"1.0\"?>\n";
	echo "<recordset>\n";
	while ($row = sqlRMArray(dbFetchAssoc($result))) { 
		$ary = array(	'uid' => $row['UID'], 
						'module' => 'gallery',
						'title' => $row['title'],
						'recordalias' => $row['recordAlias'],
						'files' => 'none',
						'images' => 'uploadmultiple',
						'videos' => 'none' );

		echo arrayToXml2d($ary, 'record', '  '); 
	}
	echo "</recordset>\n";	
}

?>
