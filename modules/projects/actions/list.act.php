<?

//--------------------------------------------------------------------------------------------------
//	list all projects on the system, possibly constrained by school, grade or user
//--------------------------------------------------------------------------------------------------

	if (authHas('projects', 'show', '') == false) { do403(); }
	$page->load($installPath . 'modules/projects/actions/list.page.php');
	$page->render();

?>
