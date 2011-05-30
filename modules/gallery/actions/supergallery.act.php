<?

//--------------------------------------------------------------------------------------------------
//	display recent images from everybody
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('gallery', 'gallery_gallery', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }			// check basic permissions
	
	$page->load('modules/gallery/actions/supergallery.page.php');
	$page->allowBlockArgs('page');
	$page->render();

?>
