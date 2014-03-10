<?

//--------------------------------------------------------------------------------------------------
//*	list all galleries by creation date, title or number of images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	// check basic permissions
	//----------------------------------------------------------------------------------------------

	if (false == $user->authHas('gallery', 'gallery_gallery', 'show')) { $kapenta->page->do403(); }	

	//----------------------------------------------------------------------------------------------
	//	get order and page
	//----------------------------------------------------------------------------------------------

	$pageNo = 1;
	$orderBy = 'createdOn';
	$orderLabel = '';

	if (array_key_exists('page', $kapenta->request->args)) { $pageNo = $kapenta->request->args['page']; }
	if (array_key_exists('orderby', $kapenta->request->args)) { 

		$orderBy = $kapenta->request->args['orderby']; 

		switch($orderBy) {
			case 'createdOn':	$orderLabel = 'by creation date';	break;
			case 'title':		$orderLabel = 'by title';			break;
			case 'imagecount':	$orderLabel = 'by image count';		break;
			case 'ownerName':	$orderLabel = 'by owner';			break;
			case 'schoolName':	$orderLabel = 'by school';			break;
			default: 			$orderBy = 'createdOn';				break;	// prevent HTML inject
		}
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$kapenta->page->load('modules/gallery/actions/listall.page.php');		
	$kapenta->page->blockArgs['pageNo'] = $pageNo;								
	$kapenta->page->blockArgs['orderBy'] = $orderBy;
	$kapenta->page->blockArgs['orderLabel'] = $orderLabel;
	$kapenta->page->blockArgs['userUID'] = $user->UID;
	$kapenta->page->blockArgs['userRa'] = $user->alias;
	$kapenta->page->title = 'awareNet - all galleries ' . $orderLabel . ' (page ' . $pageNo . ')';
	$kapenta->page->render();													

?>
