<?

//--------------------------------------------------------------------------------------------------
//*	API of moblog module. No public actions. DEPRECATED
//--------------------------------------------------------------------------------------------------

	if ('public' == $kapenta->user->role) { $kapenta->page->doXmlError('not logged in'); }

	//--------------------------------------------------------------------------------------------------
	//	list records owned by the current user
	//--------------------------------------------------------------------------------------------------

	if ('myrecords' == $kapenta->request->ref) {
		$sql = ''
		 . "select * from moblog_post where"
		 . " createdBy='" . $kapenta->db->addMarkup($kapenta->user->UID) . "' order by title";

		$result = $kapenta->db->query($sql);
	
		echo "<?xml version=\"1.0\"?>\n";
		echo "<recordset>\n";
		while ($row = $kapenta->db->rmArray($kapenta->db->fetchAssoc($result))) { 
			$ary = array(
				'uid' => $row['UID'], 
				'module' => 'moblog',
				'title' => $row['title'],
				'recordalias' => $row['alias'],
				'files' => 'none',
				'images' => 'uploadmultiple',
				'videos' => 'none'
			);

		//echo arrayToXml2d($ary, 'record', '  '); 
		//TODO: re-implement this
		}
		echo "</recordset>\n";	
	}

?>
