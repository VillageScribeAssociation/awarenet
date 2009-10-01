<?

//--------------------------------------------------------------------------------------------------
//	display all files in the database ordered by date added to the system
//--------------------------------------------------------------------------------------------------

	if (authHas('files', 'list', '') == false) { do403(); }

	$page->load($installPath . 'modules/files/actions/showall.page.php');
	$page->allowBlockArgs('page,refMod');
	$page->render();

?>
