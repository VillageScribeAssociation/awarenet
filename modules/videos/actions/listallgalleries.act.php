<?

//--------------------------------------------------------------------------------------------------
//*	list all video galleries by creation date, title or number of images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------

	if (false == $user->authHas('videos', 'videos_gallery', 'show')) { $page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	get order and page
	//----------------------------------------------------------------------------------------------

	$pageNo = 1;
	$orderBy = 'createdOn';
	$orderLabel = '';
	$origin = 'user';
	$originLabel = 'by awareNet users';

	if (true == array_key_exists('page', $kapenta->request->args)) { $pageNo = $kapenta->request->args['page']; }
	if (true == array_key_exists('orderby', $kapenta->request->args)) { $orderBy = $kapenta->request->args['orderby']; }
	if (true == array_key_exists('origin', $kapenta->request->args)) { $origin = $kapenta->request->args['origin']; }

	if ('3rdparty' == $origin) { $originLabel = 'by awareNet partners'; }

	switch($orderBy) {
		case 'createdOn':	$orderLabel = 'by creation date';	break;
		case 'title':		$orderLabel = 'by title';			break;
		case 'imagecount':	$orderLabel = 'by image count';		break;
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/videos/actions/listallgalleries.page.php');		
	$kapenta->page->blockArgs['pageNo'] = $pageNo;								
	$kapenta->page->blockArgs['orderBy'] = $orderBy;
	$kapenta->page->blockArgs['orderLabel'] = $orderLabel;
	$kapenta->page->blockArgs['userUID'] = $user->UID;
	$kapenta->page->blockArgs['userRa'] = $user->alias;
	$kapenta->page->blockArgs['origin'] = $origin;
	$kapenta->page->blockArgs['originlabel'] = $originLabel;
	$page->title = 'awareNet - all video galleries ' . $orderLabel . ' (page ' . $pageNo . ')';
	$kapenta->page->render();													

?>
