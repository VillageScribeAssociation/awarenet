<?

//--------------------------------------------------------------------------------------------------
//	display recent images from everybody
//--------------------------------------------------------------------------------------------------

	if (authHas('gallery', 'show', '') == false) { do403(); }			// check basic permissions
	
	$page->load($installPath . 'modules/gallery/actions/supergallery.page.php');
	$page->allowBlockArgs('page');
	$page->render();

?>
