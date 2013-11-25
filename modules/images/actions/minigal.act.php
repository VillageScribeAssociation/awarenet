<?

//--------------------------------------------------------------------------------------------------
//*	make a small gallery (as on a blog post or calendar item)
//--------------------------------------------------------------------------------------------------
//TODO: this is a very old action, make blocks to take care of this

	//----------------------------------------------------------------------------------------------
	//	check req arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $kapenta->request->args))
		{ $page->do404('refModule not given', true); }

	if (false == array_key_exists('refModel', $kapenta->request->args))
		{ $page->do404('refModel not given', true); }

	if (false == array_key_exists('refUID', $kapenta->request->args))
		{ $page->do404('refUID not given', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404("No such module."); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404("No such owner."); }

	//----------------------------------------------------------------------------------------------
	//	load all images associated with this record
	//----------------------------------------------------------------------------------------------
	$show = '';
	$rows = array();

	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('images_image', '*', $conditions, 'weight');

	//	$sql = "select * from Images_Image where refModule='" . $db->addMarkup($kapenta->request->args['refmodule']) 
	//		. "' and refUID='" . $db->addMarkup($kapenta->request->args['refuid']) . "' order by weight";
			
	if (0 == count($range)) {
		$kapenta->page->load('modules/images/actions/minigal.page.php');
		$page->content = '';
		$kapenta->page->render();
		die();
	}
		
	$index = 0;
	foreach ($range as $row) {
		if ('' == $show) { $show = $row['UID']; }
		$rows[$row['UID']] = $row;
		$rows[$row['UID']]['index'] = $index;			//TODO: make this less clumsy
	}
		
	if (array_key_exists('show', $kapenta->request->args)) 
		{ $show = $db->addMarkup($kapenta->request->args['show']); }
		
	//------------------------------------------------------------------------------------------
	//	show the current image
	//------------------------------------------------------------------------------------------
	$imgRow = $rows[$show];
	$attrib = '';
	if ($imgRow['attribName'] != '') {
		$attrib = $db->removeMarkup($imgRow['attribName']);
		if ($imgRow['attribURL'] != '') {
			$attrib = "<a href='" . $db->removeMarkup($imgRow['attribURL']) . "'>$attrib</a>";
		}
	}
		
	$img = "
	<img src='/images/width560/" . $imgRow['alias'] . "' /><br/>
	<b>" . $imgRow['title'] . "</b> " . $db->removeMarkup($imgRow['caption']) . "
	<a href='#' onClick=\"window.parent.location='%%serverPath%%images/full/" 
	. $imgRow['alias'] . "'\">[view larger]</a>
	<br/>
	<small>image licence: " . $imgRow['licence'] . " $attrib</small>
	<br/>
	";
		
	//----------------------------------------------------------------------------------------------
	//	make the nav bar
	//----------------------------------------------------------------------------------------------
	$nav = '';
	foreach($rows as $UID => $row) {
		$thumbUrl = '/images/thumbsm/' . $row['alias'];
		$navUrl = '/images/minigal'
			. '/refModule_' . $refModule
			. '/refModel_' . $refModel  
			. '/refUID_' . $refUID . '/show_' . $UID . '/';
				
		$nav .= "<a href='" . $navUrl . "'><img src='" . $thumbUrl . "' border='0' /></a>\n";
	}
		
	$html = $img . $nav;
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/images/actions/minigal.page.php');
	$page->content = $html;
	$kapenta->page->render();

?>
