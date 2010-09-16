<?

//--------------------------------------------------------------------------------------------------------------
//	list static pages
//--------------------------------------------------------------------------------------------------------------

	if ($user->authHas('home', 'Home_Static', 'show', 'TODO:UIDHERE') == false) { return false; }
	$page->load('modules/static/actions/list.page.php');
	$page->render();

?>
