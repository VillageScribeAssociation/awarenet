<?

//--------------------------------------------------------------------------------------------------
//	API of projects module. No public actions.
//--------------------------------------------------------------------------------------------------

if ($user->data['ofGroup'] == 'public') { doXmlError('not logged in'); }

//--------------------------------------------------------------------------------------------------
//	list records owned by the current user
//--------------------------------------------------------------------------------------------------


if ($request['ref'] == 'myrecords') {

	$sql = "select projects.UID, projects.title, projects.recordAlias " 
		 . "from projectmembers, projects "
		 . "where projectmembers.userUID='" . $user->data['UID'] . "' "
		 . "and projects.UID=projectmembers.projectUID";

	$result = dbQuery($sql);
	
	echo "<?xml version=\"1.0\"?>\n";
	echo "<recordset>\n";
	while ($row = sqlRMArray(dbFetchAssoc($result))) { 
		$ary = array(	'uid' => $row['UID'], 
						'module' => 'projects',
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
