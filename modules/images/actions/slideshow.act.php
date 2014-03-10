<?

//--------------------------------------------------------------------------------------------------
//*	make a slideshow  //TODO: this is very old code, replace with standard page/block templates
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('refModule', $kapenta->request->args))
		{ $kapenta->page->do404('refModule not given', true); }

	if (false == array_key_exists('refModel', $kapenta->request->args))
		{ $kapenta->page->do404('refModel not given', true); }

	if (false == array_key_exists('refUID', $kapenta->request->args))
		{ $kapenta->page->do404('refUID not given', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	load all images associated with this record
	//----------------------------------------------------------------------------------------------
	$show = '';
	$rows = array();

	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";  
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

	$range = $kapenta->db->loadRange('images_image', '*', $conditions, 'weight');

	//$sql = "select * from Images_Image where refModule='" . $kapenta->db->addMarkup($kapenta->request->args['refmodule']) 
	//		. "' and refUID='" . $kapenta->db->addMarkup($kapenta->request->args['refuid']) . "' order by weight";

				
	$index = 0;

	foreach ($range as $row) {
		if ($show == '') { $show = $row['UID']; }
		$rows[$row['UID']] = $row;
		$rows[$row['UID']]['index'] = $index;
	}
		
	if (array_key_exists('show', $kapenta->request->args)) { $show = $kapenta->db->addMarkup($kapenta->request->args['show']); }
		
	//----------------------------------------------------------------------------------------------
	//	show the current image
	//----------------------------------------------------------------------------------------------
		
	$imgRow = $rows[$show];
	$img = "
		<img src='/images/slide/" . $imgRow['alias'] . "' /><br/>
		<b>" . $imgRow['title'] . "</b> " . $imgRow['caption'] . "
		<a href='#' onClick=\"window.parent.location='%%serverPath%%images/show/" 
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
		
	$kapenta->page->load('modules/images/actions/slideshow.page.php');
	$kapenta->page->content = $html;
	$kapenta->page->render();

?>
