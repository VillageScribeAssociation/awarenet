<?

//--------------------------------------------------------------------------------------------------
//	display all images in the database ordered by dat added to the system
//--------------------------------------------------------------------------------------------------

	if (authHas('images', 'list', '') == false) { do403(); }

	$page->load($installPath . 'modules/images/actions/showall.page.php');
	$page->allowBlockArgs('page,refMod');
	$page->render();

?>
