<?

//--------------------------------------------------------------------------------------------------
//*	list all projects on the system, possibly constrained by school, grade or user
//--------------------------------------------------------------------------------------------------

	if (false == $user->authHas('projects', 'Projects_Project', 'show')) { $page->do403(); }
	//TODO: arguments for pagination, etc

	$page->load('modules/projects/actions/list.page.php');
	$page->render();

?>
