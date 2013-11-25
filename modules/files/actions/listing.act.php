<?

//--------------------------------------------------------------------------------------------------
//*	make a file listing
//--------------------------------------------------------------------------------------------------
//TODO: fix this up

	if ( (array_key_exists('refmodule', $kapenta->request->args)) 
	   AND (array_key_exists('refuid', $kapenta->request->args)) ) {
	  
		//------------------------------------------------------------------------------------------
		//	load all files associated with this record
		//------------------------------------------------------------------------------------------

		$rows = array();
		$sql = "select * from files_file where refModule='" . $db->addMarkup($kapenta->request->args['refmodule']) 
			. "' and refUID='" . $db->addMarkup($kapenta->request->args['refuid']) . "' order by weight";
			
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
			$labels['thumbUrl'] = '/themes/%%defaultTheme%%/images/arrow_down.jpg';
			$labels['dnUrl'] = '/files/dn/' . sqlRemoveMarkup($row['title']);
			$html .= $theme->replaceLabels($labels, $theme->loadBlock('modules/files/listing.block.php'));
		}
		
		$kapenta->page->load('modules/files/listing.page.php');
		$page->content = $html;
		$kapenta->page->render();
		
	}

?>
