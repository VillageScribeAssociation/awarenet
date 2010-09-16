<?

//--------------------------------------------------------------------------------------------------
//	make a small gallery (as on a blog post or calendar item)
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('refmodule', $req->args)) 
	   AND (array_key_exists('refuid', $req->args)) ) {
	  
		//------------------------------------------------------------------------------------------
		//	load all images associated with this record
		//------------------------------------------------------------------------------------------
		$show = '';
		$rows = array();
		$sql = "select * from Images_Image where refModule='" . $db->addMarkup($req->args['refmodule']) 
			. "' and refUID='" . $db->addMarkup($req->args['refuid']) . "' order by weight";
			
		$result = $db->query($sql);
		
		if ($db->numRows($result) == 0) {
			$page->load('modules/images/actions/minigal.page.php');
			$page->content = '';
			$page->render();
			die();
		}
		
		$index = 0;
		while ($row = $db->fetchAssoc($result)) {
			
			if ($show == '') { $show = $row['UID']; }
			$rows[$row['UID']] = $row;
			$rows[$row['UID']]['index'] = $index;
		}
		
		if (array_key_exists('show', $req->args)) 
			{ $show = $db->addMarkup($req->args['show']); }
		
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
		<img src='/images/width560/" . $imgRow['alias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . sqlRemoveMarkup($imgRow['caption']) . "
		<a href='#' onClick=\"window.parent.location='" . $serverPath . "images/full/" 
		. $imgRow['alias'] . "'\">[view larger]</a>
		<br/>
		<small>image licence: " . $imgRow['licence'] . " $attrib</small>
		<br/>
		";
		
		//------------------------------------------------------------------------------------------
		//	make the nav bar
		//------------------------------------------------------------------------------------------
		$nav = '';
		foreach($rows as $UID => $row) {
			$thumbUrl = '/images/thumbsm/' . $row['alias'];
			$navUrl = '/images/minigal/refModule_' . $req->args['refmodule'] 
				. '/refUID_' . $req->args['refuid'] . '/show_' . $UID . '/';
				
			$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl . "' border='0' /></a>\n";
		}
		
		$html = $img . $nav;
		
		$page->load('modules/images/actions/minigal.page.php');
		$page->content = $html;
		$page->render();

	}

?>
