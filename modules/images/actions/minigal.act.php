<?

//--------------------------------------------------------------------------------------------------
//	make a small gallery (as on a blog post or calendar item)
//--------------------------------------------------------------------------------------------------

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
		
		if (dbNumRows($result) == 0) {
			$page->load($installPath . 'modules/images/actions/minigal.page.php');
			$page->data['content'] = '';
			$page->render();
			die();
		}
		
		$index = 0;
		while ($row = dbFetchAssoc($result)) {
			
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
		$attrib = '';
		if ($imgRow['attribName'] != '') {
			$attrib = sqlRemoveMarkup($imgRow['attribName']);
			if ($imgRow['attribURL'] != '') {
				$attrib = "<a href='" . sqlRemoveMarkup($imgRow['attribURL']) . "'>$attrib</a>";
			}
		}
		
		$img = "
		<img src='/images/width560/" . $imgRow['recordAlias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . sqlRemoveMarkup($imgRow['caption']) . "
		<a href='#' onClick=\"window.parent.location='" . $serverPath . "images/full/" 
		. $imgRow['recordAlias'] . "'\">[view larger]</a>
		<br/>
		<small>image licence: " . $imgRow['licence'] . " $attrib</small>
		<br/>
		";
		
		//------------------------------------------------------------------------------------------
		//	make the nav bar
		//------------------------------------------------------------------------------------------
		$nav = '';
		foreach($rows as $UID => $row) {
			$thumbUrl = '/images/thumbsm/' . $row['recordAlias'];
			$navUrl = '/images/minigal/refModule_' . $request['args']['refmodule'] 
				. '/refUID_' . $request['args']['refuid'] . '/show_' . $UID . '/';
				
			$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl . "' border='0' /></a>\n";
		}
		
		$html = $img . $nav;
		
		$page->load($installPath . 'modules/images/actions/minigal.page.php');
		$page->data['content'] = $html;
		$page->render();

	}

?>
