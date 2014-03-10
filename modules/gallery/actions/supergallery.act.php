<?

//--------------------------------------------------------------------------------------------------
//*	display recent images from everybody
//--------------------------------------------------------------------------------------------------
//reqopt: page - page to display [string]

	//----------------------------------------------------------------------------------------------
	// permissions
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('gallery', 'gallery_gallery', 'show')) { $kapenta->page->do403(); }
	
	$kapenta->page->load('modules/gallery/actions/supergallery.page.php');
	$kapenta->page->allowBlockArgs('page');
	$kapenta->page->render();

?>
