<?

//--------------------------------------------------------------------------------------------------
//	display all images in the database ordered by dat added to the system
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('images', 'images_image', 'list', 'TODO:UIDHERE') == false) { $page->do403(); }

	$page->load('modules/images/actions/showall.page.php');
	$page->allowBlockArgs('page,refMod');
	$page->render();

?>
