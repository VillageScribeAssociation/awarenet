<?

//--------------------------------------------------------------------------------------------------------------
//	list static pages
//--------------------------------------------------------------------------------------------------------------

	if ($user->authHas('home', 'Home_Static', 'show', 'TODO:UIDHERE') == false) { return false; }
	$kapenta->page->load('modules/static/actions/list.page.php');
	$kapenta->page->render();

?>
