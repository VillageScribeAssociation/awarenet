<?

//--------------------------------------------------------------------------------------------------
//	make a slideshow  //TODO: this should be a block
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refmodule', $req->args))
		{ $page->do404('module not given', true); }


	if (false == array_key_exists('refuid', $req->args))
		{ $page->do404('UID not given', true); }

	//----------------------------------------------------------------------------------------------
	//	load all images associated with this record
	//----------------------------------------------------------------------------------------------
	$show = '';
	$rows = array();
		$sql = "select * from Images_Image where refModule='" . $db->addMarkup($req->args['refmodule']) 
			. "' and refUID='" . $db->addMarkup($req->args['refuid']) . "' order by weight";
			
		$result = $db->query($sql);
		$index = 0;
		while ($row = $db->fetchAssoc($result)) {
			$row = $db->rmArray($row);
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
		$img = "
		<img src='/images/slide/" . $imgRow['alias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . $imgRow['caption'] . "
		<a href='#' onClick=\"window.parent.location='" . $serverPath . "images/show/" 
		. $imgRow['alias'] . "'\">[view larger]</a>
		<br/><br/>
		";
		
		//------------------------------------------------------------------------------------------
		//	make the nav bar
		//------------------------------------------------------------------------------------------
		
		$nav = '';
		foreach($rows as $UID => $row) {
			$thumbUrl = '/images/thumbsm/' . $row['alias'];
			$navUrl = '/images/slideshow/refModule_' . $req->args['refmodule'] 
				. '/refUID_' . $req->args['refuid'] . '/show_' . $UID . '/';
				
			$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl 
			     . "' border='0' alt='" . $row['title'] . "' /></a>\n";
		}
		
		$html = $img . $nav;
		
		$page->load('modules/images/actions/slideshow.page.php');
		$page->content = $html;
		$page->render();
		
	}

?>
