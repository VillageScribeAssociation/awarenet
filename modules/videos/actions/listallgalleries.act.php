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

	if (array_key_exists('page', $req->args)) { $pageNo = $req->args['page']; }
	if (array_key_exists('orderby', $req->args)) { $orderBy = $req->args['orderby']; }

	switch($orderBy) {
		case 'createdOn':	$orderLabel = 'by creation date';	break;
		case 'title':		$orderLabel = 'by title';			break;
		case 'imagecount':	$orderLabel = 'by image count';		break;
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load('modules/videos/actions/listallgalleries.page.php');		
	$page->blockArgs['pageNo'] = $pageNo;								
	$page->blockArgs['orderBy'] = $orderBy;
	$page->blockArgs['orderLabel'] = $orderLabel;
	$page->blockArgs['userUID'] = $user->UID;
	$page->blockArgs['userRa'] = $user->alias;
	$page->title = 'awareNet - all video galleries ' . $orderLabel . ' (page ' . $pageNo . ')';
	$page->render();													

?>
