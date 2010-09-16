<?

//--------------------------------------------------------------------------------------------------
//	list all schools on the system
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('schools', 'Schools_School', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }
	$page->load('modules/schools/actions/list.page.php');
	$page->render();

?>
