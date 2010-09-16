<?

//--------------------------------------------------------------------------------------------------
//	list all projects on the system, possibly constrained by school, grade or user
//--------------------------------------------------------------------------------------------------

	if ($user->authHas('projects', 'Projects_Project', 'show', 'TODO:UIDHERE') == false) { $page->do403(); }
	$page->load('modules/projects/actions/list.page.php');
	$page->render();

?>
