<?

//--------------------------------------------------------------------------------------------------
//*	make a file listing
//--------------------------------------------------------------------------------------------------
//TODO: fix this up

	if ( (array_key_exists('refmodule', $req->args)) 
	   AND (array_key_exists('refuid', $req->args)) ) {
	  
		//------------------------------------------------------------------------------------------
		//	load all files associated with this record
		//------------------------------------------------------------------------------------------

		$rows = array();
		$sql = "select * from Files_File where refModule='" . $db->addMarkup($req->args['refmodule']) 
			. "' and refUID='" . $db->addMarkup($req->args['refuid']) . "' order by weight";
			
		//TODO: $db->loadRange

		$result = $db->query($sql);
		$index = 0;
		while ($row = $db->fetchAssoc($result)) {
			if ($show == '') { $show = $row['UID']; }
			$rows[$row['UID']] = $row;
			$rows[$row['UID']]['index'] = $index;
		}
				
		//------------------------------------------------------------------------------------------
		//	make list of files
		//------------------------------------------------------------------------------------------
		
		$html = '';
		foreach($rows as $UID => $row) {
			$f = name File();
			$f->loadArray($row);
			$labels = $f->extArray();
			$labels['thumbUrl'] = '/themes/clockface/images/arrow_down.jpg';
			$labels['dnUrl'] = '/files/dn/' . sqlRemoveMarkup($row['title']);
			$html .= $theme->replaceLabels($labels, $theme->loadBlock('modules/files/listing.block.php'));
		}
		
		$page->load('modules/files/listing.page.php');
		$page->content = $html;
		$page->render();
		
	}

?>
