<?

//--------------------------------------------------------------------------------------------------
//	make a slideshow
//--------------------------------------------------------------------------------------------------
//TODO: add javascript nav buttons and reload (avoid jumpy iframe)

	if ( (array_key_exists('refmodule', $request['args'])) 
	   AND (array_key_exists('refuid', $request['args'])) ) {
	  
		//------------------------------------------------------------------------------------------
		//	load all images associated with this record
		//------------------------------------------------------------------------------------------
		$show = '';
		$rows = array();
		$sql = "select * from images where refModule='" . sqlMarkup($request['args']['refmodule']) 
			. "' and refUID='" . sqlMarkup($request['args']['refuid']) . "' order by weight";
			
		$result = dbQuery($sql);
		$index = 0;
		while ($row = dbFetchAssoc($result)) {
			$row = sqlRMArray($row);
			if ($show == '') { $show = $row['UID']; }
			$rows[$row['UID']] = $row;
			$rows[$row['UID']]['index'] = $index;
		}
		
		if (array_key_exists('show', $request['args'])) 
			{ $show = sqlMarkup($request['args']['show']); }
		
		//------------------------------------------------------------------------------------------
		//	show the current image
		//------------------------------------------------------------------------------------------
		
		$imgRow = $rows[$show];
		$img = "
		<img src='/images/slide/" . $imgRow['recordAlias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . $imgRow['caption'] . "
		<a href='#' onClick=\"window.parent.location='" . $serverPath . "images/show/" 
		. $imgRow['recordAlias'] . "'\">[view larger]</a>
		<br/><br/>
		";
		
		//------------------------------------------------------------------------------------------
		//	make the nav bar
		//------------------------------------------------------------------------------------------
		
		$nav = '';
		foreach($rows as $UID => $row) {
			$thumbUrl = '/images/thumbsm/' . $row['recordAlias'];
			$navUrl = '/images/slideshow/refModule_' . $request['args']['refmodule'] 
				. '/refUID_' . $request['args']['refuid'] . '/show_' . $UID . '/';
				
			$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl 
			     . "' border='0' alt='" . $row['title'] . "' /></a>\n";
		}
		
		$html = $img . $nav;
		
		$page->load($installPath . 'modules/images/actions/slideshow.page.php');
		$page->data['content'] = $html;
		$page->render();
		
	}

?>
