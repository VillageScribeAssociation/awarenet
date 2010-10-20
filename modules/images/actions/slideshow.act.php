<?

//--------------------------------------------------------------------------------------------------
//*	make a slideshow  //TODO: this is very old code, replace wiuth standard page/block templates
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $req->args))
		{ $page->do404('refModule not given', true); }

	if (false == array_key_exists('refModel', $req->args))
		{ $page->do404('refModel not given', true); }

	if (false == array_key_exists('refUID', $req->args))
		{ $page->do404('refUID not given', true); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	load all images associated with this record
	//----------------------------------------------------------------------------------------------
	$show = '';
	$rows = array();

	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";  
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('Images_Image', '*', $conditions, 'weight');

	//$sql = "select * from Images_Image where refModule='" . $db->addMarkup($req->args['refmodule']) 
	//		. "' and refUID='" . $db->addMarkup($req->args['refuid']) . "' order by weight";

				
	$index = 0;

	foreach ($range as $row) {
		if ($show == '') { $show = $row['UID']; }
		$rows[$row['UID']] = $row;
		$rows[$row['UID']]['index'] = $index;
	}
		
	if (array_key_exists('show', $req->args)) { $show = $db->addMarkup($req->args['show']); }
		
	//----------------------------------------------------------------------------------------------
	//	show the current image
	//----------------------------------------------------------------------------------------------
		
	$imgRow = $rows[$show];
	$img = "
		<img src='/images/slide/" . $imgRow['alias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . $imgRow['caption'] . "
		<a href='#' onClick=\"window.parent.location='" . $serverPath . "images/show/" 
		. $imgRow['alias'] . "'\">[view larger]</a>
		<br/><br/>
		";
		
	//----------------------------------------------------------------------------------------------
	//	make the nav bar
	//----------------------------------------------------------------------------------------------
		
	$nav = '';
	foreach($rows as $UID => $row) {
		$thumbUrl = '/images/thumbsm/' . $row['alias'];
		$navUrl = '/images/slideshow'
			. '/refModule_' . $refModule
			. '/refModel_' . $refModel  
			. '/refUID_' . $refUID
			. '/show_' . $UID . '/';
				
		$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl 
		     . "' border='0' alt='" . $row['title'] . "' /></a>\n";
	}
		
	$html = $img . $nav;
		
	$page->load('modules/images/actions/slideshow.page.php');
	$page->content = $html;
	$page->render();

?>
