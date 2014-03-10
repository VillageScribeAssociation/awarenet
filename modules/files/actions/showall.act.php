<?

//--------------------------------------------------------------------------------------------------
//*	display all files in the database ordered by date added to the system
//--------------------------------------------------------------------------------------------------

	//TODO: fix auth
	//if (authHas('files', 'list', '') == false) { $kapenta->page->do403(); }

	$kapenta->page->load('modules/files/actions/showall.page.php');
	$kapenta->page->allowBlockArgs('page,refMod');
	$kapenta->page->render();

?>
