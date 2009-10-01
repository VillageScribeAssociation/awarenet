<?

//--------------------------------------------------------------------------------------------------
//	make a slideshow
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('refmodule', $request['args'])) 
	   AND (array_key_exists('refuid', $request['args'])) ) {
	  
		//------------------------------------------------------------------------------------------
		//	load all files associated with this record
		//------------------------------------------------------------------------------------------

		$rows = array();
		$sql = "select * from files where refModule='" . sqlMarkup($request['args']['refmodule']) 
			. "' and refUID='" . sqlMarkup($request['args']['refuid']) . "' order by weight";
			
		$result = dbQuery($sql);
		$index = 0;
		while ($row = dbFetchAssoc($result)) {
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
			$html .= replaceLabels($labels, loadBlock('modules/files/listing.block.php'));
		}
		
		$page->load($installPath . 'modules/files/listing.page.php');
		$page->data['content'] = $html;
		$page->render();
		
	}

?>
