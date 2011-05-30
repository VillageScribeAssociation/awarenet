<?

//--------------------------------------------------------------------------------------------------
//	API of moblog module. No public actions.
//--------------------------------------------------------------------------------------------------

if ('public' == $user->role) { $page->doXmlError('not logged in'); }

//--------------------------------------------------------------------------------------------------
//	list records owned by the current user
//--------------------------------------------------------------------------------------------------

if ('myrecords' == $req->ref) {
	$sql = "select * from moblog_post where createdBy='" . $db->addMarkup($user->UID) . "' order by title";
	$result = $db->query($sql);
	
	echo "<?xml version=\"1.0\"?>\n";
	echo "<recordset>\n";
	while ($row = $db->rmArray($db->fetchAssoc($result))) { 
		$ary = array(	'uid' => $row['UID'], 
						'module' => 'moblog',
						'title' => $row['title'],
						'recordalias' => $row['alias'],
						'files' => 'none',
						'images' => 'uploadmultiple',
						'videos' => 'none' );

		//echo arrayToXml2d($ary, 'record', '  '); 
		//TODO: re-implement this
	}
	echo "</recordset>\n";	
}

?>
